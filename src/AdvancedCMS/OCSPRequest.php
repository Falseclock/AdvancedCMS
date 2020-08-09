<?php
/**
 * OCSPRequest
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/AdvancedCMS
 */

namespace Falseclock\AdvancedCMS;

use Adapik\CMS\CMSBase;
use Adapik\CMS\Exception\FormatException;
use Adapik\CMS\Signature;
use FG\ASN1\Universal\Sequence;

/**
 * Class OCSPRequest
 *
 * @see     Maps\OCSPRequest
 * @package Falseclock\AdvancedCMS
 */
class OCSPRequest extends CMSBase
{
    const CONTENT_TYPE = 'application/ocsp-request';
    const OCSP_DEFAULT_NONCE_LENGTH = 16;
    const OID_OCSPNonce = '1.3.6.1.5.5.7.48.1.2';

    /**
     * @var Sequence
     */
    protected $object;

    /**
     * @param string $content
     * @return OCSPRequest
     * @throws FormatException
     */
    public static function createFromContent(string $content)
    {
        return new self(self::makeFromContent($content, Maps\OCSPRequest::class, Sequence::class));
    }

    /**
     * @return TBSRequest
     */
    public function getTBSRequest()
    {
        return new TBSRequest($this->object->getChildren()[0]);
    }

    /**
     * FIXME: возвращать объект
     * @return Signature|null
     * @throws FormatException
     */
    public function getOptionalSignature()
    {
        $children = $this->object->getChildren();

        if (count($children) == 2) {
            return Signature::createFromContent($children[1]->getBinaryContent());
        }

        return null;
    }
}
