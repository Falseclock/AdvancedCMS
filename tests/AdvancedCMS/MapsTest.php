<?php
declare(strict_types=1);

namespace Falseclock\AdvancedCMS\Test;

use Falseclock\AdvancedCMS\Maps\OCSPRequest;
use Falseclock\AdvancedCMS\Maps\OCSPResponse;
use Falseclock\AdvancedCMS\Maps\OCSPResponseStatus;
use Falseclock\AdvancedCMS\Maps\PKIStatusInfo;
use Falseclock\AdvancedCMS\Maps\Request;
use Falseclock\AdvancedCMS\Maps\ResponseBytes;
use Falseclock\AdvancedCMS\Maps\TBSRequest;
use Falseclock\AdvancedCMS\Maps\TimeStampRequest;
use Falseclock\AdvancedCMS\Maps\TimeStampResponse;
use PHPUnit\Framework\TestCase;
use ReflectionClassConstant;

class MapsTest extends TestCase
{
    public function testForMap()
    {
        $maps = [
            OCSPRequest::class,
            OCSPResponse::class,
            OCSPResponseStatus::class,
            PKIStatusInfo::class,
            Request::class,
            ResponseBytes::class,
            TBSRequest::class,
            TimeStampRequest::class,
            TimeStampResponse::class
        ];

        foreach ($maps as $class) {
            $reflectionClassConstant = new ReflectionClassConstant($class, 'MAP');
            $value = $reflectionClassConstant->getValue();
            self::assertIsIterable($value);
            self::assertArrayHasKey('type', $value);
        }
    }
}
