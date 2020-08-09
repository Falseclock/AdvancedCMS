<?php
/**
 * TimeStampResponse
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
 * Class TimeStampResponse
 *
 * @see     Maps\TimeStampResponse
 * @package Falseclock\AdvancedCMS
 */
class TimeStampResponse extends CMSBase
{
    const CONTENT_TYPE = 'application/timestamp-reply';

    /**
     * @var Sequence
     */
    protected $object;

    /**
     * @param string $content
     * @return TimeStampResponse
     * @throws FormatException
     */
    public static function createFromContent(string $content)
    {
        return new self(self::makeFromContent($content, Maps\TimeStampResponse::class, Sequence::class));
    }

    /**
     * @return PKIStatusInfo
     */
    public function getStatusInfo()
    {
        return new PKIStatusInfo($this->object->getChildren()[0]);
    }

    /**
     * @return SignedData|null
     */
    public function getSignedData()
    {
        $children = $this->object->getChildren();

        if (count($children) == 2) {
            return new SignedData($children[1]);
        }

        return null;
    }
}
