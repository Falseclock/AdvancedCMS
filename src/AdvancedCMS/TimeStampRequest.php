<?php
/**
 * TimeStampRequest
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/AdvancedCMS
 */

namespace Falseclock\AdvancedCMS;

use Adapik\CMS\Algorithm;
use Adapik\CMS\Exception\FormatException;
use Exception;
use FG\ASN1\ImplicitlyTaggedObject;
use FG\ASN1\Universal\Boolean;
use FG\ASN1\Universal\Integer;
use FG\ASN1\Universal\NullObject;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\OctetString;
use FG\ASN1\Universal\Sequence;

/**
 * Class TimeStampRequest
 *
 * @see     Maps\TimeStampRequest
 * @package Falseclock\AdvancedCMS
 */
class TimeStampRequest extends Request
{
    const CONTENT_TYPE = 'application/timestamp-query';

    /**
     * @var Sequence
     */
    protected $object;

    /**
     * @param string $content
     * @return TimeStampRequest
     * @throws FormatException
     */
    public static function createFromContent(string $content)
    {
        return new self(self::makeFromContent($content, Maps\TimeStampRequest::class, Sequence::class));
    }

    /**
     * @param OctetString $data data which should be queried with TS request
     * @param string $hashAlgorithmOID
     * @return TimeStampRequest
     * @throws Exception
     */
    public static function createSimple(OctetString $data, string $hashAlgorithmOID = Algorithm::OID_SHA256)
    {
        $tspRequest = Sequence::create([
            # version
            Integer::create(1),
            # messageImprint
            Sequence::create([
                Sequence::create([
                    ObjectIdentifier::create($hashAlgorithmOID),
                    NullObject::create(),
                ]),
                OctetString::createFromString(Algorithm::hashValue($hashAlgorithmOID, $data->getBinaryContent()))
            ]),
            # nonce
            Integer::create(rand() << 32 | rand()),
            # certReq
            Boolean::create(true),
        ]);

        return new self($tspRequest);
    }

    /**
     * FIXME: shouldn't return ASN1Object
     * @return Boolean
     * @throws Exception
     */
    public function getCertReq()
    {
        return $this->object->findChildrenByType(Boolean::class)[0];
    }

    /**
     * FIXME: shouldn't return ASN1Object
     * @return ImplicitlyTaggedObject|null
     * @throws Exception
     */
    public function getExtensions()
    {
        $objects = $this->object->findChildrenByType(ImplicitlyTaggedObject::class);

        if (count($objects)) {
            return $objects[0];
        }

        return null;
    }

    /**
     * FIXME: shouldn't return ASN1Object
     * @return Sequence
     * @throws Exception
     */
    public function getMessageImprint()
    {
        return $this->object->findChildrenByType(Sequence::class)[0];
    }

    /**
     * FIXME: shouldn't return ASN1Object
     * @return Integer|null
     * @throws Exception
     */
    public function getNonce()
    {
        $integers = $this->object->findChildrenByType(Integer::class);
        if (count($integers) == 2) {
            return $integers[1];
        }

        return null;
    }

    /**
     * FIXME: shouldn't return ASN1Object
     * @return ObjectIdentifier
     * @throws Exception
     */
    public function getReqPolicy()
    {
        $objects = $this->object->findChildrenByType(ObjectIdentifier::class);
        if (count($objects)) {
            return $objects[0];
        }

        return null;
    }
}
