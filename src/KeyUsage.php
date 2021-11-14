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
use Adapik\CMS\Exception\FormatException;
use Adapik\CMS\Interfaces\CMSInterface;
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
     * @throws FormatException
     */
    public static function createFromContent(string $content): CMSInterface
    {
        return new self(self::makeFromContent($content, \Adapik\CMS\Maps\KeyUsage::class, BitString::class));
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
        $usage = str_split(base_convert(base64_encode($this->getBinaryContent()), 16, 2));

        foreach ($usage as $index => $value) {
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
