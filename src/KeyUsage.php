<?php
/**
 * KeyUsage
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2021 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/AdvancedCMS
 */

declare(strict_types=1);

namespace Falseclock\AdvancedCMS;

use Adapik\CMS\CMSBase;
use Adapik\CMS\Interfaces\CMSInterface;
use FG\ASN1\ASN1ObjectInterface;
use FG\ASN1\Exception\ParserException;
use FG\ASN1\Universal\BitString;

/**
 * Class KeyUsage
 *
 * @see     \Adapik\CMS\Maps\KeyUsage
 * @package Falseclock\AdvancedCMS
 */
class KeyUsage extends CMSBase
{
    public const DIGITAL_SIGNATURE = 0;
    public const NONREPUDIATION = 1;
    public const KEY_ENCIPHERMENT = 2;
    public const DATA_ENCIPHERMENT = 3;
    public const KEY_AGREEMENT = 4;
    public const KEY_CERT_SIGN = 5;
    public const CRL_SIGN = 6;
    public const ENCIPHER_ONLY = 7;
    public const DECIPHER_ONLY = 8;
    /** @var BitString $object */
    protected $object;

    /**
     * CMSBase constructor.
     *
     * @param ASN1ObjectInterface $object
     * @throws ParserException
     */
    public function __construct(ASN1ObjectInterface $object)
    {
        parent::__construct($object);

        $binary = $object->getBinaryContent();

        $this->object = BitString::fromBinary($binary);
    }

    public static function createFromContent(string $content): CMSInterface
    {
        // TODO: Implement createFromContent() method.
    }

    /**
     * @return bool
     */
    public function hasDigitalSignature(): bool
    {
        return $this->hasUsage(KeyUsage::DIGITAL_SIGNATURE);
    }

    /**
     *
     * @param int $keyUsage
     * @return bool
     * @see KeyUsage
     */
    public function hasUsage(int $keyUsage): bool
    {
        $string = str_split($this->object->getStringValue(), 2);

        $usage = "";

        for ($i = 0; $i < count($string); ++$i) {
            $skip = ($i == count($string) - 1) ? $this->object->getNumberOfUnusedBits() : 0;
            for ($j = 7; $j >= $skip; --$j) {
                $usage .= (hexdec($string[$i]) >> $j) & 1 ? "1" : "0";
            }
        }

        foreach (str_split($usage) as $index => $value) {
            if ($index == $keyUsage && (int)$value === 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function hasKeyCertSign(): bool
    {
        return $this->hasUsage(KeyUsage::KEY_CERT_SIGN);
    }
}
