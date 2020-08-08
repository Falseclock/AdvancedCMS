<?php
/**
 * UnsignedAttributes
 * @see \Adapik\CMS\UnsignedAttributes
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/CMS-EDITOR
 */

namespace Falseclock\EditCMS;

use Adapik\CMS\BasicOCSPResponse;
use Adapik\CMS\CertificateList;
use Adapik\CMS\RevocationValues;
use Adapik\CMS\TimeStampResponse;
use Adapik\CMS\TimeStampToken;
use FG\ASN1\Exception\Exception;
use FG\ASN1\Exception\ParserException;
use FG\ASN1\ExplicitlyTaggedObject;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\Sequence;
use FG\ASN1\Universal\Set;

/**
 * Class UnsignedAttributes
 *
 * @see     \Adapik\CMS\Maps\UnsignedAttributes
 * @package Adapik\CMS
 */
class UnsignedAttributes extends \Adapik\CMS\UnsignedAttributes
{
    /**
     * Sometimes having Cryptographic Message Syntax (CMS) we need to store OCSP check response for the
     * signer certificate, otherwise CMS data means nothing.
     *
     * @param BasicOCSPResponse|null $basicOCSPResponse
     *
     * @param CertificateList|null $certificateList
     * @param Sequence|null $otherRevVals
     * @return \Adapik\CMS\UnsignedAttributes
     * @throws Exception
     * @throws ParserException
     * @see \Adapik\CMS\Maps\RevocationValues
     */
    public function setRevocationValues(?BasicOCSPResponse $basicOCSPResponse = null, ?CertificateList $certificateList = null, ?Sequence $otherRevVals = null)
    {
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

        if (!is_null($otherRevVals)) {
            $binary = $otherRevVals->getBinary();

            $values[] = ExplicitlyTaggedObject::create(2,
                Sequence::create([
                        Sequence::fromBinary($binary),
                    ]
                )
            );
        }

        $revocationValues = Sequence::create([
                ObjectIdentifier::create(RevocationValues::getOid()),
                Set::create([
                        Sequence::create($values),
                    ]
                ),
            ]
        );

        $current = $this->findByOid(RevocationValues::getOid());

        if ($current) {
            $this->object->replaceChild($current, $revocationValues);
        } else {
            $this->object->appendChild($revocationValues);
        }

        return $this;
    }


    /**
     * This function will append TimeStampToken with TSTInfo or create TimeStampToken as UnsignedAttribute
     *
     * @param TimeStampResponse $response
     * @return \Adapik\CMS\UnsignedAttributes
     * @throws Exception
     * @throws ParserException
     */
    public function setTimeStampToken(TimeStampResponse $response)
    {
        $binary = $response->getTimeStampToken()->getBinary();

        $timeStampToken = Sequence::create([
                ObjectIdentifier::create(TimeStampToken::getOid()),
                Set::create([Sequence::fromBinary($binary)]),
            ]
        );

        $current = $this->findByOid(TimeStampToken::getOid());

        if ($current) {
            $this->object->replaceChild($current, $timeStampToken);
        } else {
            $this->object->appendChild($timeStampToken);
        }

        return $this;
    }
}
