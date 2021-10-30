<?php
/**
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/AdvancedCMS
 */

declare(strict_types=1);

namespace Falseclock\AdvancedCMS\Test;

use Falseclock\AdvancedCMS\PKIStatusInfo;
use Falseclock\AdvancedCMS\TimeStampResponse;

class PKIStatusInfoTest extends MainTest
{
    public function testBase()
    {
        $timeStampResponse = TimeStampResponse::createFromContent($this->getTimeStampResponse());
        $PKIStatusInfo = $timeStampResponse->getStatusInfo();

        $PKIStatusInfo = PKIStatusInfo::createFromContent($PKIStatusInfo->getBinary());

        self::assertInstanceOf(PKIStatusInfo::class, $PKIStatusInfo);
        self::assertNull($PKIStatusInfo->getFailureInfo());
        self::assertIsInt($PKIStatusInfo->getStatus());
        self::assertEquals(0, $PKIStatusInfo->getStatus());
        self::assertIsBool($PKIStatusInfo->isGranted());
        self::assertEquals(true, $PKIStatusInfo->isGranted());
        self::assertNull($PKIStatusInfo->getStatusString());
    }

    public function testBad()
    {
        $timeStampResponse = TimeStampResponse::createFromContent($this->getBadTimeStampResponse());
        $PKIStatusInfo = $timeStampResponse->getStatusInfo();
        self::assertIsInt($PKIStatusInfo->getStatus());
        self::assertEquals(2, $PKIStatusInfo->getStatus());
        self::assertIsBool($PKIStatusInfo->isGranted());
        self::assertEquals(false, $PKIStatusInfo->isGranted());

        self::assertNotNull($PKIStatusInfo->getFailureInfo());
        self::assertIsString($PKIStatusInfo->getStatusString());
    }
}