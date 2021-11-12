<?php
/**
 * Signature
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/AdvancedCMS
 */

namespace Falseclock\AdvancedCMS;

use Adapik\CMS\AlgorithmIdentifier;
use Adapik\CMS\Certificate;
use Adapik\CMS\CMSBase;
use Adapik\CMS\Exception\FormatException;
use Adapik\CMS\Interfaces\CMSInterface;
use FG\ASN1\Exception\ParserException;
use FG\ASN1\ExplicitlyTaggedObject;
use FG\ASN1\Universal\BitString;
use FG\ASN1\Universal\Sequence;

/**
 * Class Signature
 *
 * @see     Maps\Signature
 * @package Falseclock\AdvancedCMS
 */
class Signature extends CMSBase
{
    /**
     * @var Sequence
     */
    protected $object;

    /**
     * @param string $content
     * @return Signature
     * @throws FormatException
     */
    public static function createFromContent(string $content): CMSInterface
    {
        return new self(self::makeFromContent($content, Maps\Signature::class, Sequence::class));
    }

    /**
     * @return AlgorithmIdentifier
     */
    public function getSignatureAlgorithm(): AlgorithmIdentifier
    {
        $signatureAlgorithm = $this->object->getChildren()[0];

        return new AlgorithmIdentifier($signatureAlgorithm);
    }

    /**
     * @return BitString
     * @throws ParserException
     */
    public function getSignature(): BitString
    {
        $binary = $this->object->getChildren()[1]->getBinary();

        return BitString::fromBinary($binary);
    }

    /**
     * @return Certificate[]
     */
    public function getCerts(): array
    {
        $certificates = [];

        if (count($this->object->getChildren()) == 3) {
            /** @var ExplicitlyTaggedObject $certs */
            $certs = $this->object->getChildren()[2];

            foreach ($certs->getChildren() as $cert) {
                $certificates[] = new Certificate($cert->getChildren()[0]);
            }
        }
        return $certificates;
    }
}
