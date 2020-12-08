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
use Adapik\CMS\CMSBase;
use Adapik\CMS\Exception\FormatException;
use Adapik\CMS\MessageImprint;
use Exception;
use FG\ASN1\ASN1ObjectInterface;
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
class TimeStampRequest extends CMSBase
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
    public static function createFromContent(string $content): CMSBase
    {
        return new self(self::makeFromContent($content, Maps\TimeStampRequest::class, Sequence::class));
    }

    /**
     * @param OctetString|ASN1ObjectInterface $data data which should be queried with TS request
     * @param string $hashAlgorithmOID
     * @return TimeStampRequest
     * @throws Exception
     */
    public static function createSimple(OctetString $data, string $hashAlgorithmOID = Algorithm::OID_SHA256): TimeStampRequest
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
     * @return Boolean
     * @throws Exception
     * @noinspection PhpUnnecessaryFullyQualifiedNameInspection
     */
    public function getCertReq(): ?\FG\ASN1\Universal\Boolean
    {
        $return = null;
        $boolean = $this->object->findChildrenByType(Boolean::class);
        if (count($boolean) > 0) {
            $binary = $boolean[0]->getBinary();
            $return = Boolean::fromBinary($binary);
        }

        return $return;
    }

    /**
     * @return MessageImprint
     * @throws Exception
     */
    public function getMessageImprint(): MessageImprint
    {
        return new MessageImprint($this->object->findChildrenByType(Sequence::class)[0]);
    }

    /**
     * @return Integer|ASN1ObjectInterface|null
     * @throws Exception
     */
    public function getNonce()
    {
        $return = null;
        $integers = $this->object->findChildrenByType(Integer::class);
        if (count($integers) == 2) {
            $binary = $integers[1]->getBinary();
            $return = Integer::fromBinary($binary);
        }

        return $return;
    }

    /**
     * @return ObjectIdentifier|ASN1ObjectInterface
     * @throws Exception
     */
    public function getReqPolicy()
    {
        $return = null;
        $objects = $this->object->findChildrenByType(ObjectIdentifier::class);
        if (count($objects)) {
            $binary = $objects[0]->getBinary();
            $return = ObjectIdentifier::fromBinary($binary);
        }

        return $return;
    }
}
