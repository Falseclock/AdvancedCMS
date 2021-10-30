<?php
/**
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/AdvancedCMS
 */

declare(strict_types=1);

namespace Falseclock\AdvancedCMS\Test;

use Adapik\CMS\AlgorithmIdentifier;
use Adapik\CMS\CertID;
use Adapik\CMS\Extension;
use Falseclock\AdvancedCMS\OCSPRequest;
use Falseclock\AdvancedCMS\Request;
use Falseclock\AdvancedCMS\TBSRequest;
use FG\ASN1\Universal\Integer;
use FG\ASN1\Universal\OctetString;

class TBSRequestTest extends MainTest
{
    public function testBase()
    {
        $OCSPRequest = OCSPRequest::createFromContent($this->getOCSPRequest());

        $TBSRequest = $OCSPRequest->getTBSRequest();

        self::assertInstanceOf(TBSRequest::class, $TBSRequest);

        $TBSRequest = TBSRequest::createFromContent($TBSRequest->getBinary());

        $requestExtensions = $TBSRequest->getRequestExtensions();
        self::assertIsIterable($requestExtensions);

        foreach ($requestExtensions as $extension) {
            self::assertInstanceOf(Extension::class, $extension);
            $extension->isCritical();
            $extension->getExtensionValue();
            $extension->getExtensionId();
        }

        $requestList = $TBSRequest->getRequestList();

        foreach ($requestList as $request) {
            $requestedCertificate = $request->getRequestedCertificate();

            $request = Request::createFromContent($request->getBinary());

            self::assertInstanceOf(CertID::class, $requestedCertificate);
            self::assertInstanceOf(AlgorithmIdentifier::class, $requestedCertificate->getHashAlgorithm());
            self::assertInstanceOf(Integer::class, $requestedCertificate->getSerialNumber());
            self::assertInstanceOf(OctetString::class, $requestedCertificate->getIssuerKeyHash());
            self::assertInstanceOf(OctetString::class, $requestedCertificate->getIssuerNameHash());

            self::assertNull($request->getSingleRequestExtensions());

        }

        self::assertNull($TBSRequest->getRequesterName());
    }
}