<?php
/**
 * PKIStatusInfo
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/AdvancedCMS
 */

namespace Falseclock\AdvancedCMS;

use Adapik\CMS\CMSBase;
use Adapik\CMS\Exception\FormatException;
use FG\ASN1\Universal\Sequence;

/**
 * Class PKIStatusInfo
 *
 * @see     Maps\PKIStatusInfo
 * @package Falseclock\AdvancedCMS
 */
class PKIStatusInfo extends CMSBase
{
    /**
     * @var Sequence
     */
    protected $object;

    /**
     * @param string $content
     * @return PKIStatusInfo
     * @throws FormatException
     */
    public static function createFromContent(string $content)
    {
        return new self(self::makeFromContent($content, Maps\PKIStatusInfo::class, Sequence::class));
    }

    /**
     * @return bool
     */
    public function isGranted()
    {
        return $this->getStatus() == 0;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        $integer = $this->object->getChildren()[0];

        return intval($integer->__toString());
    }

    /**
     * @return string
     */
    public function getFailInfo()
    {
        $children = $this->object->getChildren();

        if (count($children) == 2) {
            return $children[1]->__toString();
        }
        return null;
    }
}
