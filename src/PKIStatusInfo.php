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
use Adapik\CMS\Interfaces\CMSInterface;
use Exception;
use FG\ASN1\Universal\BitString;
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
    public static function createFromContent(string $content): CMSInterface
    {
        return new self(self::makeFromContent($content, Maps\PKIStatusInfo::class, Sequence::class));
    }

    /**
     * @return bool
     */
    public function isGranted(): bool
    {
        return $this->getStatus() == 0;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        $integer = $this->object->getChildren()[0];

        return intval($integer->__toString());
    }

    /**
     * @return string|null
     * @throws Exception
     */
    public function getStatusString(): ?string
    {
        $children = $this->object->getChildren();
        if (count($children) > 1) {
            $utf8Strings = $this->object->findChildrenByType(Sequence::class);
            if (count($utf8Strings) > 0) {
                return $utf8Strings[0]->__toString();
            }
        }

        return null;
    }

    /**
     * @return string|null
     * @throws Exception
     */
    public function getFailureInfo(): ?string
    {
        $children = $this->object->getChildren();

        if (count($children) > 1) {
            $bitStrings = $this->object->findChildrenByType(BitString::class);
            return $bitStrings[0]->__toString();
        }
        return null;
    }
}
