<?php
/**
 * TimeStampToken
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/AdvancedCMS
 */

namespace Falseclock\AdvancedCMS;

use FG\ASN1\Exception\ParserException;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\Sequence;
use FG\ASN1\Universal\Set;

/**
 * Class TimeStampToken
 *
 * @see     \Adapik\CMS\Maps\TimeStampToken
 * @package Falseclock\AdvancedCMS
 */
class TimeStampToken extends \Adapik\CMS\TimeStampToken
{
    /**
     * @param TimeStampResponse $timeStampResponse
     * @return TimeStampToken
     * @throws ParserException
     */
    public static function createFromTimeStampResponse(TimeStampResponse $timeStampResponse): TimeStampToken
    {
        return new self(self::sequenceFromTimeStampResponse($timeStampResponse));
    }

    /**
     * @param $timeStampResponse
     * @return Sequence
     * @throws ParserException
     */
    public static function sequenceFromTimeStampResponse(TimeStampResponse $timeStampResponse): Sequence
    {
        $binary = $timeStampResponse->getSignedData()->getBinary();

        return Sequence::create([
                ObjectIdentifier::create(TimeStampToken::getOid()),
                Set::create([Sequence::fromBinary($binary)]),
            ]
        );
    }
}
