<?php
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