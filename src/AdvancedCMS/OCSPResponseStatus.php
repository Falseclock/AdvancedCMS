<?php
/**
 * OCSPResponseStatus
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/CMS-EDITOR
 */

namespace Falseclock\AdvancedCMS;

use Adapik\CMS\CMSBase;
use Adapik\CMS\Exception\FormatException;
use FG\ASN1\Universal\Enumerated;

/**
 * Class OCSPResponseStatus
 *
 * @see     Maps\OCSPResponseStatus
 * @package Falseclock\AdvancedCMS
 */
class OCSPResponseStatus extends CMSBase
{
    /**
     * @var Enumerated
     */
    protected $object;

    /**
     * @param string $content
     * @return OCSPResponseStatus
     * @throws FormatException
     */
    public static function createFromContent(string $content)
    {
        return new self(self::makeFromContent($content, Maps\OCSPResponseStatus::class, Enumerated::class));
    }

    /**
     * @return mixed
     */
    public function getMapping()
    {
        return Maps\OCSPResponseStatus::MAP['mapping'];
    }

    /**
     * Returns status of request. 0 = is OK, other - NOT
     *
     * @return bool
     */
    public function isSuccessful()
    {
        return intval($this->object->value) === 0;
    }
}
