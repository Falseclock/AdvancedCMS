<?php
/**
 * EncapsulatedContentInfo
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/AdvancedCMS
 */

namespace Falseclock\AdvancedCMS;

use FG\ASN1\Exception\Exception;
use FG\ASN1\ExplicitlyTaggedObject;
use FG\ASN1\Universal\OctetString;

/**
 * Class EncapsulatedContentInfo
 *
 * @see     \Adapik\CMS\Maps\EncapsulatedContentInfo
 * @package Falseclock\AdvancedCMS
 */
class EncapsulatedContentInfo extends \Adapik\CMS\EncapsulatedContentInfo
{
    /**
     * Insert or update data content
     *
     * @param OctetString $octetString
     *
     * @return EncapsulatedContentInfo
     * @throws \Exception
     */
    public function setEContent(OctetString $octetString): self
    {
        $children = $this->object->getChildren();

        if (count($children) == 2) {
            $this->object->replaceChild($children[1], $octetString);
        } else {
            $this->object->appendChild(ExplicitlyTaggedObject::create(0, $octetString));
        }

        return $this;
    }

    /**
     * Removing content if exist in case of necessity.
     * Actually we sign content hash and storing content not always strict.
     * Moreover, content can be very huge and heavy
     *
     * @return EncapsulatedContentInfo
     * @throws Exception
     */
    public function unSetEContent(): self
    {
        $children = $this->object->getChildren();
        if (count($children) == 2) {
            $eContent = $children[1];
            $this->object->removeChild($eContent);
        }

        return $this;
    }
}
