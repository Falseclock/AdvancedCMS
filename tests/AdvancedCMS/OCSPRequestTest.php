<?php

namespace Falseclock\AdvancedCMS\Test;

use Adapik\CMS\Algorithm;
use Adapik\CMS\Certificate;
use Falseclock\AdvancedCMS\OCSPRequest;
use Falseclock\AdvancedCMS\OCSPResponse;
use Falseclock\AdvancedCMS\SignedData;
use Falseclock\AdvancedCMS\TBSRequest;

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

    /**
     * @todo implement
     */
    //public function testWithSignature() {
    //    self::assertInstanceOf(Signature::class, $OCSPRequest->getOptionalSignature());
    //}
}
