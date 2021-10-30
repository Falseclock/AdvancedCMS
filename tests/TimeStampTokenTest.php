<?php
/**
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/AdvancedCMS
 */

declare(strict_types=1);

namespace Falseclock\AdvancedCMS\Test;

use Falseclock\AdvancedCMS\TimeStampResponse;
use Falseclock\AdvancedCMS\TimeStampToken;
use FG\ASN1\Universal\Sequence;

class TimeStampTokenTest extends MainTest
{
    public function testBasic()
    {
        $TimeStampResponse = TimeStampResponse::createFromContent($this->getTimeStampResponse());
        self::assertInstanceOf(TimeStampResponse::class, $TimeStampResponse);
        self::assertInstanceOf(TimeStampToken::class, TimeStampToken::createFromTimeStampResponse($TimeStampResponse));
        self::assertInstanceOf(Sequence::class, TimeStampToken::sequenceFromTimeStampResponse($TimeStampResponse));
    }
}