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
use Adapik\CMS\Extension;
use Adapik\CMS\GeneralName;
use Exception;
use FG\ASN1\ExplicitlyTaggedObject;
use FG\ASN1\Universal\Sequence;

/**
 * Class TBSRequest
 *
 * @see     Maps\TBSRequest
 * @see     Maps\OCSPRequest
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
    public static function createFromContent(string $content): self
    {
        return new self(self::makeFromContent($content, Maps\TBSRequest::class, Sequence::class));
    }

    /**
     * @return GeneralName
     * @throws Exception
     */
    public function getRequesterName(): ?GeneralName
    {
        /** @var ExplicitlyTaggedObject[] $tags */
        $tags = $this->object->findChildrenByType(ExplicitlyTaggedObject::class);
        foreach ($tags as $tag) {
            if ($tag->getIdentifier()->getTagNumber() == 1) {
                return new GeneralName($tag->getChildren()[0]);
            }
        }
        return null;
    }

    /**
     * @return Request[]
     * @throws Exception
     */
    public function getRequestList(): array
    {
        $requests = [];
        /** @var Sequence[] $requestList */
        $requestList = $this->object->findChildrenByType(Sequence::class);
        foreach ($requestList[0]->getChildren() as $sequence) {
            $requests[] = new Request($sequence);
        }

        return $requests;
    }

    /**
     * @return Extension[]
     * @throws Exception
     */
    public function getRequestExtensions(): array
    {
        /** @var ExplicitlyTaggedObject[] $tags */
        $tags = $this->object->findChildrenByType(ExplicitlyTaggedObject::class);

        $extensions = [];

        foreach ($tags as $tag) {
            if ($tag->getIdentifier()->getTagNumber() == 2) {
                foreach ($tag->getChildren()[0]->getChildren() as $child) {
                    $extensions[] = new Extension($child);
                }
            }
        }

        return $extensions;
    }
}
