<?php
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
        self::assertNull($PKIStatusInfo->getFailInfo());
        self::assertIsInt($PKIStatusInfo->getStatus());
        self::assertEquals(0, $PKIStatusInfo->getStatus());
        self::assertIsBool($PKIStatusInfo->isGranted());
        self::assertEquals(true, $PKIStatusInfo->isGranted());
    }

    // TODO: test failed response
}