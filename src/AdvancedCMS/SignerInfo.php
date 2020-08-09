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

use Exception;
use FG\ASN1\ExplicitlyTaggedObject;
use FG\ASN1\ImplicitlyTaggedObject;
use FG\ASN1\Universal\NullObject;

/**
 * Class SignerInfo
 *
 * @see     \Adapik\CMS\Maps\SignerInfo
 * @package Falseclock\AdvancedCMS
 */
class SignerInfo extends \Adapik\CMS\SignerInfo
{
    /**
     * @return void
     * @throws Exception
     */
    protected function createUnsignedAttributesIfNotExist(): void
    {
        /**
         * 1. First check do we have unsignedAttrs or not, cause it is optional fields and create it if not.
         * Always push it to the end of child.
         */
        $UnsignedAttribute = $this->getUnsignedAttributes();

        if (is_null($UnsignedAttribute)) {
            $UnsignedAttribute = $this->createUnsignedAttribute();
            $this->object->appendChild($UnsignedAttribute);
        }
    }

    /**
     * @return UnsignedAttributes
     * @throws Exception
     */
    public function getUnsignedAttributes()
    {
        return new UnsignedAttributes($this->findUnsignedAttributes());
    }

    /**
     * @return ImplicitlyTaggedObject
     */
    protected function createUnsignedAttribute()
    {
        return ExplicitlyTaggedObject::create(1, NullObject::create());
    }
}
