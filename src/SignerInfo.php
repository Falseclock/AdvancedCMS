<?php
/**
 * SignerInfo
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/AdvancedCMS
 */

namespace Falseclock\AdvancedCMS;

use Adapik\CMS\CMSBase;
use Adapik\CMS\Exception\FormatException;
use Adapik\CMS\UnsignedAttribute;
use Exception;
use FG\ASN1\Exception\ParserException;
use FG\ASN1\ExplicitlyTaggedObject;
use FG\ASN1\Universal\NullObject;
use FG\ASN1\Universal\Sequence;

/**
 * Class SignerInfo
 *
 * @see     \Adapik\CMS\Maps\SignerInfo
 * @package Falseclock\AdvancedCMS
 */
class SignerInfo extends \Adapik\CMS\SignerInfo
{
    /**
     * @param string $content
     *
     * @return SignerInfo
     * @throws FormatException
     */
    public static function createFromContent(string $content): CMSBase
    {
        return new self(self::makeFromContent($content, \Adapik\CMS\Maps\SignerInfo::class, Sequence::class));
    }

    /**
     * @param UnsignedAttribute $newAttribute
     * @return $this
     * @throws \FG\ASN1\Exception\Exception
     * @throws ParserException
     * @throws Exception
     */
    public function addUnsignedAttribute(UnsignedAttribute $newAttribute): SignerInfo
    {
        $UnsignedAttributes = $this->getUnsignedAttributes();
        if (is_null($UnsignedAttributes)) {
            $UnsignedAttributes = $this->createUnsignedAttributes();
        }

        $oid = $newAttribute->getIdentifier()->__toString();

        if (is_null($UnsignedAttributes->getByOid($oid))) {
            $UnsignedAttributes->appendAttribute($newAttribute);
        } else {
            $UnsignedAttributes->replaceAttribute($oid, $newAttribute);
        }

        return $this;
    }

    /**
     * @return UnsignedAttributes|null
     * @throws Exception
     */
    public function getUnsignedAttributes(): ?\Adapik\CMS\UnsignedAttributes
    {
        $unsignedAttributes = $this->findUnsignedAttributes();

        if ($unsignedAttributes) {
            return new UnsignedAttributes($unsignedAttributes);
        }
        return null;
    }


    /**
     * @return UnsignedAttributes
     * @throws Exception
     */
    protected function createUnsignedAttributes(): ?UnsignedAttributes
    {
        $UnsignedAttribute = $this->getUnsignedAttributes();

        if (is_null($UnsignedAttribute)) {
            $UnsignedAttribute = ExplicitlyTaggedObject::create(1, NullObject::create());
            $UnsignedAttribute->removeChild($UnsignedAttribute->getChildren()[0]);
            $this->object->appendChild($UnsignedAttribute);
        }

        return $this->getUnsignedAttributes();
    }

    /**
     * @return $this
     * @throws \FG\ASN1\Exception\Exception
     * @throws Exception
     */
    public function deleteUnsignedAttributes(): SignerInfo
    {
        $unsignedAttributes = $this->findUnsignedAttributes();

        if ($unsignedAttributes) {
            $this->object->removeChild($unsignedAttributes);
        }

        return $this;
    }
}
