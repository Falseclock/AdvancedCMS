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
use Falseclock\AdvancedCMS\SignedData;
use Falseclock\AdvancedCMS\TimeStampResponse;

class TimeStampResponseTest extends MainTest
{
    public function testBase()
    {
        $timeStampResponse = TimeStampResponse::createFromContent($this->getTimeStampResponseTSA());
        self::assertInstanceOf(TimeStampResponse::class, $timeStampResponse);

        self::assertInstanceOf(SignedData::class, $timeStampResponse->getSignedData());
        self::assertInstanceOf(PKIStatusInfo::class, $timeStampResponse->getStatusInfo());
        self::assertInstanceOf(SignedData::class, SignedData::createFromContent($timeStampResponse->getSignedData()->getBinary()));
    }
}