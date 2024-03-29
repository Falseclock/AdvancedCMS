<?php
/**
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/AdvancedCMS
 */

declare(strict_types=1);

namespace Falseclock\AdvancedCMS\Test;

use Adapik\CMS\BasicOCSPResponse;
use Falseclock\AdvancedCMS\OCSPResponse;
use Falseclock\AdvancedCMS\ResponseBytes;

class ResponseBytesTest extends MainTest
{
    public function testMethods()
    {
        $ocspResponse = OCSPResponse::createFromContent($this->getOCSPResponse());
        $responseBytes = $ocspResponse->getResponseBytes();

        $responseBytes = ResponseBytes::createFromContent($responseBytes->getBinary());

        self::assertInstanceOf(ResponseBytes::class, $responseBytes);
        self::assertIsString($responseBytes->getResponse());
        self::assertIsString($responseBytes->getResponseType());
        self::assertEquals(OCSPResponse::OID_OCSP_BASIC, $responseBytes->getResponseType());
        self::assertInstanceOf(BasicOCSPResponse::class, BasicOCSPResponse::createFromContent($responseBytes->getResponse()));
    }
}
