<?php
/**
 * Certificate
 * @see \Adapik\CMS\Certificate
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/AdvancedCMS
 */

declare(strict_types=1);

namespace Falseclock\AdvancedCMS;

use Adapik\CMS\Exception\FormatException;
use Adapik\CMS\Interfaces\CMSInterface;
use DateTime;
use Exception;
use FG\ASN1\ASN1ObjectInterface;
use FG\ASN1\Exception\ParserException;
use FG\ASN1\Universal\BitString;
use FG\ASN1\Universal\Sequence;

class Certificate extends \Adapik\CMS\Certificate
{
    const OID_EKU_OCSP_SIGNING = '1.3.6.1.5.5.7.3.9';
    const OID_EKU_TIME_STAMPING = '1.3.6.1.5.5.7.3.8';

    /**
     * CMSBase constructor.
     *
     * @param ASN1ObjectInterface $object
     */
    public function __construct(ASN1ObjectInterface $object)
    {
        parent::__construct($object);
        $this->object = $object;
    }

    /**
     * Overriding parent method to return self instance
     *
     * @param string $content
     * @return Certificate
     * @throws FormatException
     */
    public static function createFromContent(string $content): CMSInterface
    {
        return new self(self::makeFromContent($content, \Adapik\CMS\Maps\Certificate::class, Sequence::class));
    }

    /**
     * Check whether certificate valid for given date.
     * If DateTime not provided, validation for current date will be performed.
     *
     * @param DateTime|null $subjectDate
     * @return Verification
     * @throws Exception
     */
    public function verifyDate(?DateTime $subjectDate = null): Verification
    {
        // Если нам не передали дату, то мы должны проверить на текущую дату
        if (is_null($subjectDate)) {
            $subjectDate = new DateTime();
        }

        $startDate = DateTime::createFromFormat('Y-m-d\TH:i:sP', $this->getValidNotBefore());
        $endDate = DateTime::createFromFormat('Y-m-d\TH:i:sP', $this->getValidNotAfter());

        // Если время окончания меньше, еще не наступило или прям совпадает с текущей датой
        if ($endDate->getTimestamp() < $subjectDate->getTimestamp()) {
            return new Verification(Verification::CRT_NOT_VALID_AFTER, false, $this);
        }

        // Если время начала действия сертификата еще не наступило
        if ($startDate->getTimestamp() >= $subjectDate->getTimestamp()) {
            return new Verification(Verification::CRT_NOT_VALID_BEFORE, false, $this);
        }

        return new Verification(Verification::CRT_DATE_VALID, true);
    }

    /**
     * Get Signature binary value without leading zeroes
     * @return string
     * @throws ParserException
     */
    public function getSignatureValue(): string
    {
        return substr($this->getSignature()->getBinaryContent(), 1);
    }

    /**
     * @param string $oid
     * @return bool
     * @throws ParserException
     */
    public function hasExtendedKeyUsage(string $oid): bool
    {
        foreach ($this->getExtendedKeyUsage() as $value) {
            if ($value === $oid) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return BitString|CMSInterface
     * @throws ParserException
     */
    public function getKeyUsage(): ?KeyUsage
    {
        $usage = $this->getExtension(self::OID_EXTENSION_KEY_USAGE);

        return $usage ? new KeyUsage($usage->getExtensionValue()) : null;
    }
}
