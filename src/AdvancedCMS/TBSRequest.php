<?php
/**
 * TBSRequest
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/AdvancedCMS
 */

namespace Falseclock\AdvancedCMS;

use Adapik\CMS\CMSBase;
use Adapik\CMS\Exception\FormatException;
use Exception;
use FG\ASN1\ExplicitlyTaggedObject;
use FG\ASN1\Universal\Sequence;

/**
 * Class TBSRequest
 *
 * @see     Maps\TBSRequest
 * @package Falseclock\AdvancedCMS
 */
class TBSRequest extends CMSBase
{
    /**
     * @var Sequence
     */
    protected $object;

    /**
     * @param string $content
     * @return TBSRequest
     * @throws FormatException
     */
    public static function createFromContent(string $content)
    {
        return new self(self::makeFromContent($content, Maps\TBSRequest::class, Sequence::class));
    }

    /**
     * FIXME: shouldn't return ASN1Object
     * @return ExplicitlyTaggedObject|null
     * @throws Exception
     */
    public function getRequestorName()
    {
        /** @var ExplicitlyTaggedObject[] $tags */
        $tags = $this->object->findChildrenByType(ExplicitlyTaggedObject::class);
        foreach ($tags as $tag) {
            if ($tag->getIdentifier()->getTagNumber() == 1) {
                return $tag;
            }
        }
        return null;
    }

    /**
     * FIXME: shouldn't be created statically
     * @return Request[]
     * @throws FormatException
     */
    public function getRequestList()
    {
        $requests = [];
        /** @var Sequence[] $requestList */
        $requestList = $this->object->findChildrenByType(Sequence::class);
        foreach ($requestList as $sequence) {
            $requests[] = Request::createFromContent($sequence->getBinaryContent());
        }

        return $requests;
    }

    /**
     * FIXME: shouldn't return ASN1Object
     * @return ExplicitlyTaggedObject|null
     * @throws Exception
     */
    public function getRequestExtensions()
    {
        /** @var ExplicitlyTaggedObject[] $tags */
        $tags = $this->object->findChildrenByType(ExplicitlyTaggedObject::class);

        foreach ($tags as $tag) {
            if ($tag->getIdentifier()->getTagNumber() == 2) {
                return $tag;
            }
        }

        return null;
    }
}
