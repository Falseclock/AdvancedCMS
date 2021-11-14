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

final class KeyUsage
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
}
