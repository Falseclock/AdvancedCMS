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
	 * @return \Adapik\CMS\SignerInfo
	 * @throws FormatException
	 */
	public static function createFromContent(string $content)
	{
		return new self(self::makeFromContent($content, \Adapik\CMS\Maps\SignerInfo::class, Sequence::class));
	}

    /**
     * @param UnsignedAttribute $newAttribute
     * @return $this
     * @throws \FG\ASN1\Exception\Exception
     * @throws ParserException
     */
    public function addUnsignedAttribute(UnsignedAttribute $newAttribute)
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
    public function getUnsignedAttributes()
    {
        $unsignedAttributes = $this->findUnsignedAttributes();
        if ($unsignedAttributes) {
            return new UnsignedAttributes($this->findUnsignedAttributes());
        }
        return null;
    }


    /**
     * @return UnsignedAttributes
     * @throws Exception
     */
    protected function createUnsignedAttributes()
    {
        $UnsignedAttribute = $this->getUnsignedAttributes();

        if (is_null($UnsignedAttribute)) {
            $UnsignedAttribute = ExplicitlyTaggedObject::create(1, NullObject::create());
            $UnsignedAttribute->removeChild($UnsignedAttribute->getChildren()[0]);
            $this->object->appendChild($UnsignedAttribute);
        }

        return $this->getUnsignedAttributes();
    }
}
