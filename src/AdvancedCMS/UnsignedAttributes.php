<?php
/**
 * UnsignedAttributes
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/AdvancedCMS
 */

namespace Falseclock\AdvancedCMS;

use Adapik\CMS\BasicOCSPResponse;
use Adapik\CMS\CertificateList;
use Adapik\CMS\RevocationValues;
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
 * @package Falseclock\AdvancedCMS
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
     * @param Sequence|null $otherRevValues
     * @return \Adapik\CMS\UnsignedAttributes
     * @throws Exception
     * @throws ParserException
     * @see \Adapik\CMS\Maps\RevocationValues
     */
    public function setRevocationValues(?BasicOCSPResponse $basicOCSPResponse = null, ?CertificateList $certificateList = null, ?Sequence $otherRevValues = null)
    {
        if (is_null($basicOCSPResponse) and is_null($certificateList) and is_null($otherRevValues)) {
            throw new \Exception("At least 1 parameter must be not null");
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
        $timeStampTokenSequence = TimeStampToken::sequenceFromTimeStampResponse($response);

        $current = $this->findByOid(TimeStampToken::getOid());

        if ($current) {
            $this->object->replaceChild($current, $timeStampTokenSequence);
        } else {
            $this->object->appendChild($timeStampTokenSequence);
        }

        return $this;
    }
}
