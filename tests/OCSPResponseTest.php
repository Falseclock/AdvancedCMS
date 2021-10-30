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
use Falseclock\AdvancedCMS\OCSPResponseStatus;
use Falseclock\AdvancedCMS\ResponseBytes;
use FG\ASN1\Universal\Sequence;

class OCSPResponseTest extends MainTest
{
    public function testMethods()
    {
        $ocspResponse = OCSPResponse::createFromContent($this->getOCSPResponse());

        self::assertInstanceOf(BasicOCSPResponse::class, $ocspResponse->getBasicOCSPResponse());
        self::assertInstanceOf(OCSPResponseStatus::class, $ocspResponse->getResponseStatus());
        self::assertInstanceOf(ResponseBytes::class, $ocspResponse->getResponseBytes());
    }

    public function testNullResponseBytes()
    {
        $binaryData = base64_decode($this->getOCSPResponse());
        $ocspResponseSequence = Sequence::fromBinary($binaryData);

        // simulating
        $ocspResponseSequence->removeChild($ocspResponseSequence->getChildren()[1]);

        $ocspResponse = OCSPResponse::createFromContent($ocspResponseSequence->getBinary());

        self::assertNull($ocspResponse->getBasicOCSPResponse());
        self::assertNull($ocspResponse->getResponseBytes());
        self::assertInstanceOf(OCSPResponseStatus::class, $ocspResponse->getResponseStatus());
    }
}
