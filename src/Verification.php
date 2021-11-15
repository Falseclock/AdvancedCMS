<?php
/**
 * Verification
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2021 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/AdvancedCMS
 */

declare(strict_types=1);

namespace Falseclock\AdvancedCMS;

class Verification
{
    public const CRT_NOT_VALID_AFTER = "Certificate already expired";
    public const CRT_NOT_VALID_BEFORE = "Certificate not valid yet";
    public const CRT_DATE_VALID = "Certificate date valid";
    public const CRT_INTERMEDIATE_NOT_FOUND = "Intermediate certificate not found";
    public const CRT_NOT_VALID_SIGNATURE = "Certificate signature not valid";
    public const CRT_HAS_NO_KEY_USAGE = "Certificate does not have such key usage";
    public const SIGN_HAS_NO_TST_INFO = "Sign does not have independent TSP";
    public const TST_INFO_CANT_BE_VERIFIED = "tSTInfo not verified";
    public const SIGN_HAS_NO_REVOCATION_VALUES = "Sign has no revocationValues";
    public const REV_HAS_NO_OCSP_RESPONSE = "BasicOCSPResponse not found in revocationValues";
    public const OCSP_HAS_NO_REQUIRED_CERTIFICATE = "OCSP response does not have check for required certificate";
    public const OCSP_STATUS_IS_REVOKED = "Certificate is revoked";
    public const OCSP_STATUS_IS_UNKNOWN = "Certificate status is unknown";
    public const OCSP_STATUS_EXPIRED = "Too long time distance between OCSP check and sign date";
    public const OCSP_NOT_VALID_SIGNATURE = "OCSP response signature not valid";

    /** @var string Name of verification */
    protected $name;
    /** @var bool TRUE - is ok, FALSE - is not, NULL - like a warning,  means verification did not perform. */
    protected $isVerified = null;
    /** @var null different values checked against */
    protected $subject = null;

    /**
     * @param string $name
     * @param bool $isVerified
     * @param null $subject
     */
    public function __construct(string $name, bool $isVerified = null, $subject = null)
    {
        $this->name = $name;
        $this->isVerified = $isVerified;
        $this->subject = $subject;
    }

    /**
     * @return null
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isVerified(): ?bool
    {
        return $this->isVerified;
    }
}
