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

use Adapik\CMS\CMSBase;
use Adapik\CMS\Exception\FormatException;
use Adapik\CMS\Interfaces\CMSInterface;
use Adapik\CMS\MessageImprint;
use Exception;
use FG\ASN1\ASN1ObjectInterface;
use FG\ASN1\Universal\Boolean;
use FG\ASN1\Universal\Integer;
use FG\ASN1\Universal\ObjectIdentifier;
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
    public static function createFromContent(string $content): CMSInterface
    {
        return new self(self::makeFromContent($content, Maps\TimeStampRequest::class, Sequence::class));
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
     * @return ASN1ObjectInterface|null
     * @throws Exception
     */
    public function getNonce(): ?ASN1ObjectInterface
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
     * @return ASN1ObjectInterface
     * @throws Exception
     */
    public function getReqPolicy(): ?ASN1ObjectInterface
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
