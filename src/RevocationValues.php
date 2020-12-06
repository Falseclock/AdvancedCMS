<?php
/**
 * RevocationValues
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/AdvancedCMS
 */

namespace Falseclock\AdvancedCMS;

use Adapik\CMS\BasicOCSPResponse;
use Adapik\CMS\CertificateList;
use Exception;
use FG\ASN1\Exception\ParserException;
use FG\ASN1\ExplicitlyTaggedObject;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\Sequence;
use FG\ASN1\Universal\Set;

/**
 * Class RevocationValues
 *
 * @see     \Adapik\CMS\Maps\RevocationValues
 * @package Falseclock\AdvancedCMS
 */
class RevocationValues extends \Adapik\CMS\RevocationValues
{
    /**
     * @param BasicOCSPResponse|null $basicOCSPResponse
     * @param CertificateList|null $certificateList
     * @param Sequence|null $otherRevValues
     * @return RevocationValues
     * @throws ParserException
     */
    public static function createFromOCSPResponse(?BasicOCSPResponse $basicOCSPResponse = null, ?CertificateList $certificateList = null, ?Sequence $otherRevValues = null): self
    {
        return new self(self::sequenceFromOCSPResponse($basicOCSPResponse, $certificateList, $otherRevValues));
    }

    /**
     * @param BasicOCSPResponse|null $basicOCSPResponse
     * @param CertificateList|null $certificateList
     * @param Sequence|null $otherRevValues
     * @return Sequence
     * @throws ParserException
     */
    public static function sequenceFromOCSPResponse(?BasicOCSPResponse $basicOCSPResponse = null, ?CertificateList $certificateList = null, ?Sequence $otherRevValues = null): Sequence
    {
        if (is_null($basicOCSPResponse) and is_null($certificateList) and is_null($otherRevValues)) {
            throw new Exception("At least 1 parameter must be not null");
        }

        $values = [];

        if (!is_null($basicOCSPResponse)) {
            $binary = $basicOCSPResponse->getBinary();

            $values[] = ExplicitlyTaggedObject::create(1,
                Sequence::create([
                        Sequence::fromBinary($binary),
                    ]
                )
            );
        }

        if (!is_null($certificateList)) {
            $binary = $certificateList->getBinary();

            $values[] = ExplicitlyTaggedObject::create(0,
                Sequence::create([
                        Sequence::fromBinary($binary),
                    ]
                )
            );
        }

        if (!is_null($otherRevValues)) {
            $binary = $otherRevValues->getBinary();

            $values[] = ExplicitlyTaggedObject::create(2,
                Sequence::create([
                        Sequence::fromBinary($binary),
                    ]
                )
            );
        }

        return Sequence::create([
                ObjectIdentifier::create(\Adapik\CMS\RevocationValues::getOid()),
                Set::create([
                        Sequence::create($values),
                    ]
                ),
            ]
        );
    }
}
