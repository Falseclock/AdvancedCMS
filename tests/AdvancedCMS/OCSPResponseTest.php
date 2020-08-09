<?php

namespace Falseclock\AdvancedCMS\Test;

use Adapik\CMS\BasicOCSPResponse;
use Falseclock\AdvancedCMS\OCSPResponse;
use Falseclock\AdvancedCMS\OCSPResponseStatus;
use Falseclock\AdvancedCMS\ResponseBytes;

class OCSPResponseTest extends MainTest
{
    public function testMethods()
    {
        $ocspResponse = OCSPResponse::createFromContent($this->getOCSPResponse());

        self::assertInstanceOf(BasicOCSPResponse::class, $ocspResponse->getBasicOCSPResponse());
        self::assertInstanceOf(OCSPResponseStatus::class, $ocspResponse->getResponseStatus());
        self::assertInstanceOf(ResponseBytes::class, $ocspResponse->getResponseBytes());
    }
}
