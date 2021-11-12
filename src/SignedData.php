<?php
/**
 * SignedData
 * @see \Adapik\CMS\SignedData
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/AdvancedCMS
 */

namespace Falseclock\AdvancedCMS;

use Adapik\CMS\Algorithm;
use Adapik\CMS\Certificate;
use Adapik\CMS\CMSBase;
use Adapik\CMS\Exception\FormatException;
use Adapik\CMS\Interfaces\CMSInterface;
use Exception;
use Falseclock\AdvancedCMS\Exception\SignedDataValidationException;
use FG\ASN1\ExplicitlyTaggedObject;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\Sequence;

/**
 * Class SignedData
 *
 * @see     \Adapik\CMS\Maps\SignedData
 * @package Falseclock\AdvancedCMS
 */
class SignedData extends \Adapik\CMS\SignedData
{
    const OID_SIGNED_DATA = "1.2.840.113549.1.7.2";

    /**
     * Overriding parent method to return self instance
     *
     * @param string $content
     * @return SignedData
     * @throws FormatException
     * @inheritdoc
     */
    public static function createFromContent(string $content): CMSInterface
    {
        return new self(self::makeFromContent($content, \Adapik\CMS\Maps\SignedData::class, Sequence::class));
    }

    /**
     * @param SignedData $signedData
     *
     * @return $this
     * @throws Exception
     */
    public function mergeCMS(SignedData $signedData): SignedData
    {
        $initialContent = $this->getSignedDataContent();
        $newContent = $signedData->getSignedDataContent();

        /**
         * @see \Adapik\CMS\Maps\SignedDataContent
         * Append
         * 1. digestAlgorithms
         * 2. certificates
         * 3. crl
         * 4. signerInfos
         */

        foreach ($newContent->getDigestAlgorithmIdentifiers() as $digestAlgorithmIdentifier) {
            $initialContent->appendDigestAlgorithmIdentifier($digestAlgorithmIdentifier);
        }

        foreach ($newContent->getCertificateSet() as $certificate) {
            $initialContent->appendCertificate($certificate);
        }

        /*        $revocationInfoChoices = $newContent->getRevocationInfoChoices();
                if ($revocationInfoChoices) {
                    foreach ($revocationInfoChoices as $revocationInfoChoice) {
                        $initialContent->appendRevocationInfoChoices($revocationInfoChoice);
                    }
                }*/

        foreach ($newContent->getSignerInfoSet() as $signerInfo) {
            $initialContent->appendSignerInfo($signerInfo);
        }

        return $this;
    }

    /**
     * Message content
     * @return SignedDataContent
     * @throws Exception
     */
    public function getSignedDataContent(): SignedDataContent
    {
        $SignedDataContent = $this->object->findChildrenByType(ExplicitlyTaggedObject::class)[0];

        return new SignedDataContent($SignedDataContent->getChildren()[0]);
    }

    /**
     * @throws FormatException
     * @throws Exception
     */
    public function verify()
    {
        $cmsContentTypeOid = $this->getTypeOid();

        // 1. Check all Digest Algorithms inside
        $this->checkAlgorithms();

        // 2. Check signed data exist
        if (is_null($this->getSignedDataContent()->getEncapsulatedContentInfo()->getEContent())) {
            throw new SignedDataValidationException("No electronic content present in SignedData");
        }

        $signerInfos = $this->getSignedDataContent()->getSignerInfoSet();
        $signersCertificates = $this->getSignedDataContent()->getCertificateSet();

        foreach ($signerInfos as $signer) {
            // Get signer certificate
            $signerCertificate = $this->getSignerCertificate($signersCertificates, $signer);

            $this->verifyCertificateChain($signerCertificate);
        }
    }

    /**
     * Get CMS OID type
     * @return string
     */
    public function getTypeOid(): string
    {
        /** @var ObjectIdentifier $type */
        $type = $this->object->getChildren()[0];

        return $type->__toString();
    }

    /**
     * Check that CMS uses registered hashing algorithms
     * @throws FormatException
     * @throws Exception
     */
    private function checkAlgorithms()
    {
        $availableHashes = hash_algos();

        $digestAlgorithmIdentifiers = $this->getSignedDataContent()->getDigestAlgorithmIdentifiers();

        foreach ($digestAlgorithmIdentifiers as $algorithmIdentifier) {

            $hashFunction = Algorithm::byOid($algorithmIdentifier->getAlgorithmOid());

            if (!in_array($hashFunction, $availableHashes)) {
                throw new SignedDataValidationException("Hash algorithm used in SignedData not registered system-wide");
            }
        }
    }

    /**
     * Actually certificates stored in the same order as signatures, but who know how CMS was created and
     * what is the order was used
     * @throws SignedDataValidationException
     * @throws Exception
     */
    private function getSignerCertificate(array $certificates, SignerInfo $signerInfo): Certificate
    {
        $signerCertificateSerialNumber = $signerInfo->getIssuerAndSerialNumber()->getSerialNumber();

        foreach ($certificates as $certificate) {
            if ($certificate->getSerial() === $signerCertificateSerialNumber) {
                return $certificate;
            }
        }
        throw new SignedDataValidationException("Can't find certificate related to sign");
    }

    public function verifyCertificateChain(Certificate $certificate)
    {
        // 1. Verifies digital signature of x509 certificate against a public key

    }
}
