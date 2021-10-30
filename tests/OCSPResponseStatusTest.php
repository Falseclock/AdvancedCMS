<?php
/**
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/AdvancedCMS
 */

declare(strict_types=1);

namespace Falseclock\AdvancedCMS\Test;

use Adapik\CMS\Exception\FormatException;
use Falseclock\AdvancedCMS\OCSPResponse;
use Falseclock\AdvancedCMS\OCSPResponseStatus;

class OCSPResponseStatusTest extends MainTest
{
    /**
     * @throws FormatException
     */
    public function testMethods()
    {
        $ocspResponse = OCSPResponse::createFromContent($this->getOCSPResponse());
        $responseStatus = $ocspResponse->getResponseStatus();

        $responseStatus->getMapping();
        self::assertEquals(true, $responseStatus->isSuccessful());

        self::assertEquals(\Falseclock\AdvancedCMS\Maps\OCSPResponseStatus::MAP['mapping'], $responseStatus->getMapping());

        $responseStatus = OCSPResponseStatus::createFromContent($responseStatus->getBinary());

        self::assertInstanceOf(OCSPResponseStatus::class, $responseStatus);
    }
}
