<?php
declare(strict_types=1);

namespace Falseclock\AdvancedCMS\Test;

use Adapik\CMS\BasicOCSPResponse;
use Falseclock\AdvancedCMS\OCSPResponse;

class ResponseBytesTest extends MainTest
{
    public function testMethods()
    {
        $ocspResponse = OCSPResponse::createFromContent($this->getOCSPResponse());
        $responseBytes = $ocspResponse->getResponseBytes();

        self::assertIsString($responseBytes->getResponse());
        self::assertIsString($responseBytes->getResponseType());
        self::assertEquals(OCSPResponse::OID_OCSP_BASIC, $responseBytes->getResponseType());
        self::assertInstanceOf(BasicOCSPResponse::class, BasicOCSPResponse::createFromContent($responseBytes->getResponse()));
    }
}
