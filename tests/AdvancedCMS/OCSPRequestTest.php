<?php
declare(strict_types=1);

namespace Falseclock\AdvancedCMS\Test;

use Adapik\CMS\Algorithm;
use Adapik\CMS\AlgorithmIdentifier;
use Adapik\CMS\Certificate;
use Adapik\CMS\GeneralName;
use Adapik\CMS\Signature;
use Falseclock\AdvancedCMS\OCSPRequest;
use Falseclock\AdvancedCMS\OCSPResponse;
use Falseclock\AdvancedCMS\SignedData;
use Falseclock\AdvancedCMS\TBSRequest;
use FG\ASN1\Universal\BitString;

class OCSPRequestTest extends MainTest
{
    public function testSimple()
    {
        $signedData = SignedData::createFromContent($this->getFullCMS());
        $certificate = $signedData->getSignedDataContent()->getCertificateSet()[0];

        $intermediateCertificate = Certificate::createFromContent($this->getIntermediateCertificate());

        $OCSPRequest = OCSPRequest::createSimple($certificate, $intermediateCertificate, Algorithm::OID_SHA256);

        self::assertInstanceOf(TBSRequest::class, $OCSPRequest->getTBSRequest());
        self::assertNull($OCSPRequest->getOptionalSignature());

        $OCSPResponse = null;

        foreach ($certificate->getOcspUris() as $url) {
            $result = $this->curlRequest($url, $OCSPRequest->getBinary(), OCSPRequest::CONTENT_TYPE, OCSPResponse::CONTENT_TYPE);

            if (!is_null($result)) {
                $OCSPResponse = OCSPResponse::createFromContent($result);
                self::assertInstanceOf(OCSPResponse::class, $OCSPResponse);
                // we need only one response
                break;
            }
        }

        self::assertNotNull($OCSPResponse);
    }

    public function testWithSignature()
    {
        $OCSPRequest = OCSPRequest::createFromContent($this->OCSPRequestWithSignature());

        $optionalSignature = $OCSPRequest->getOptionalSignature();

        self::assertInstanceOf(Signature::class, $optionalSignature);

        self::assertInstanceOf(AlgorithmIdentifier::class, $optionalSignature->getSignatureAlgorithm());
        self::assertInstanceOf(BitString::class, $optionalSignature->getSignature());
        self::assertIsIterable($optionalSignature->getCerts());

        foreach ($optionalSignature->getCerts() as $certificate) {
            self::assertInstanceOf(Certificate::class, $certificate);
            $certificate->getSignature();
            $certificate->getSignatureAlgorithm();
            $certificate->getOcspUris();
            $certificate->getSerial();
            $certificate->getSubjectKeyIdentifier();
            $certificate->getSubject();
            $certificate->getAuthorityKeyIdentifier();
            $certificate->getCertPolicies();
            $certificate->getExtendedKeyUsage();
            $certificate->getIssuer();
            $certificate->getValidNotAfter();
            $certificate->getValidNotBefore();
            $certificate->isCa();
        }

        $requesterName = $OCSPRequest->getTBSRequest()->getRequesterName();

        self::assertInstanceOf(GeneralName::class, $requesterName);

        GeneralName::createFromContent($requesterName->getBinary());
    }
}
