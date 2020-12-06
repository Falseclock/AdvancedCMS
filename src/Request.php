<?php
/**
 * Request
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/AdvancedCMS
 */

namespace Falseclock\AdvancedCMS;

use Adapik\CMS\CertID;
use Adapik\CMS\CMSBase;
use Adapik\CMS\Exception\FormatException;
use FG\ASN1\Universal\Sequence;

/**
 * Class Request
 *
 * @see     Maps\Request
 * @see     Maps\TBSRequest
 * @see     Maps\OCSPRequest
 * @package Falseclock\AdvancedCMS
 */
class Request extends CMSBase
{
    /**
     * @var Sequence
     */
    protected $object;

    /**
     * @param string $content
     * @return Request
     * @throws FormatException
     */
    public static function createFromContent(string $content)
    {
        return new self(self::makeFromContent($content, Maps\Request::class, Sequence::class));
    }

    /**
     * @return CertID
     */
    public function getRequestedCertificate()
    {
        return new CertID($this->object->getChildren()[0]);
    }

    /**
     * Not used in openssl
     * @return null
     */
    public function getSingleRequestExtensions()
    {
        return null;
    }
}