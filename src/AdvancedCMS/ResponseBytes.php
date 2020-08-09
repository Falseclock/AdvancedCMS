<?php
/**
 * ResponseBytes
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/CMS-EDITOR
 */

namespace Falseclock\AdvancedCMS;

use Adapik\CMS\CMSBase;
use Adapik\CMS\Exception\FormatException;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\OctetString;
use FG\ASN1\Universal\Sequence;

/**
 * Class ResponseBytes
 *
 * @see     Maps\ResponseBytes
 * @package Falseclock\AdvancedCMS
 */
class ResponseBytes extends CMSBase
{
    /**
     * @var Sequence
     */
    protected $object;

    /**
     * @param string $content
     * @return ResponseBytes
     * @throws FormatException
     */
    public static function createFromContent(string $content)
    {
        return new self(self::makeFromContent($content, Maps\ResponseBytes::class, Sequence::class));
    }

    /**
     * @return string
     */
    public function getResponseType()
    {
        /** @var ObjectIdentifier $objectIdentifier */
        $objectIdentifier = $this->object->getChildren()[0];

        return $objectIdentifier->__toString();
    }

    /**
     * @return string
     */
    public function getResponse()
    {
        /** @var OctetString $octetString */
        $octetString = $this->object->getChildren()[1];

        return $octetString->__toString();
    }
}
