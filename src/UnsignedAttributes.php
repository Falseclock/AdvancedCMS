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
use Adapik\CMS\Interfaces\CMSInterface;
use Adapik\CMS\UnsignedAttribute;
use FG\ASN1\Exception\Exception;
use FG\ASN1\Exception\ParserException;
use FG\ASN1\Universal\Sequence;

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
        $revocationValues = RevocationValues::sequenceFromOCSPResponse($basicOCSPResponse, $certificateList, $otherRevValues);

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

    /**
     * @return RevocationValues|null|CMSInterface
     */
    public function getRevocationValues()
    {
        return $this->getAttributeAsInstance(RevocationValues::class);
    }

    /**
     * @return TimeStampToken|CMSInterface|null
     */
    public function getTimeStampToken()
    {
        return $this->getAttributeAsInstance(TimeStampToken::class);
    }

    /**
     * @param UnsignedAttribute $unsignedAttribute
     * @return $this
     * @throws ParserException
     */
    public function appendAttribute(UnsignedAttribute $unsignedAttribute)
    {
        $binary = $unsignedAttribute->getBinary();

        $this->object->appendChild(Sequence::fromBinary($binary));

        return $this;
    }

    /**
     * @param string $oid
     * @param UnsignedAttribute $unsignedAttribute
     * @return $this
     * @throws Exception
     * @throws ParserException
     */
    public function replaceAttribute(string $oid, UnsignedAttribute $unsignedAttribute)
    {
        $binary = $unsignedAttribute->getBinary();
        $child = $this->findByOid($oid);

        $this->object->replaceChild($child, Sequence::fromBinary($binary));

        return $this;
    }
}
