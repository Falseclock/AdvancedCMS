<?php
/**
 * AlgorithmEncryption
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2021 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/AdvancedCMS
 */

declare(strict_types=1);

namespace Falseclock\AdvancedCMS;

use Adapik\CMS\Exception\FormatException;

class AlgorithmEncryption
{
    const OID_MD2 = "1.2.840.113549.1.1.2";
    const OID_MD4 = "1.2.840.113549.1.1.3";
    const OID_MD5 = '1.2.840.113549.1.1.4';
    const OID_SHA1 = '1.2.840.113549.1.1.5';
    const OID_SHA256 = '1.2.840.113549.1.1.11';
    const OID_SHA384 = "1.2.840.113549.1.1.12";
    const OID_SHA512 = "1.2.840.113549.1.1.13";
    const OID_SHA224 = "1.2.840.113549.1.1.14";

    /**
     * @param string $oid
     * @return string
     * @throws FormatException
     */
    public static function byOid(string $oid): string
    {
        switch ($oid) {
            case self::OID_MD2:
                return 'md2';
            case self::OID_MD4:
                return 'md4';
            case self::OID_MD5:
                return 'md5';
            case self::OID_SHA1:
                return 'sha1';
            case self::OID_SHA256:
                return 'sha256';
            case self::OID_SHA384:
                return 'sha384';
            case self::OID_SHA512:
                return 'sha512';
            case self::OID_SHA224:
                return 'sha224';
            default:
                throw new FormatException('Unknown Secure Hash Algorithm encryption with OID: ' . $oid);
        }
    }
}
