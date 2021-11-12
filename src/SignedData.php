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
use Adapik\CMS\Exception\FormatException;
use Adapik\CMS\Interfaces\CMSInterface;
use Adapik\CMS\PEMConverter;
use DateTime;
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
    /** @var Certificate[] */
    protected $intermediateСertificates = [];

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
     * @return Verification[]
     * @throws Exception
     * @throws FormatException
     */
    public function verify(): array
    {
        //--------------------------------------------------------------------
        foreach (["/tests/fixtures/PKI-intermediate.cer", "/tests/fixtures/PKI-ca.cer"] as $file) {
            $content = file_get_contents($_SERVER["PWD"] . $file);

            $this->addIntermediateCertificate(Certificate::createFromContent($content));
        }
        //--------------------------------------------------------------------

        $cmsContentTypeOid = $this->getTypeOid();
        $verifications = [];

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
            $signDate = DateTime::createFromFormat('Y-m-d\TH:i:sP', $signer->getSigningTime()->__toString());

            // Remember, we are in a cycle, cause several certificate checks will be performed
            $verifications[] = $signerCertificate->verifyDate($signDate);

            // No need to check further
            if (!end($verifications)->isVerified()) {
                return $verifications;
            }

            $this->verifyCertificateChain($signerCertificate, $signDate);
        }

        return $verifications;
    }

    /**
     * Add any certificate for further chain check
     * @param Certificate $certificate
     * @return $this
     * @throws Exception
     */
    public function addIntermediateCertificate(Certificate $certificate): SignedData
    {
        $this->intermediateСertificates[$certificate->getSubjectKeyIdentifier()] = $certificate;

        return $this;
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
     * @param array $certificates
     * @param SignerInfo $signerInfo
     * @return Certificate|CMSInterface
     * @throws FormatException
     * @throws SignedDataValidationException
     * @throws Exception
     */
    private function getSignerCertificate(array $certificates, SignerInfo $signerInfo): Certificate
    {
        $signerCertificateSerialNumber = $signerInfo->getIssuerAndSerialNumber()->getSerialNumber();

        foreach ($certificates as $certificate) {
            if ($certificate->getSerial() === $signerCertificateSerialNumber) {
                return Certificate::createFromContent($certificate->getBinary());
            }
        }
        throw new SignedDataValidationException("Can't find certificate related to sign");
    }

    /**
     * @param Certificate $certificate
     * @param DateTime|null $subjectDate
     * @return Verification
     * @throws Exception
     */
    public function verifyCertificateChain(Certificate $certificate, DateTime $subjectDate = null): Verification
    {
        // We have to load intermediate certificates before checking
        if (!isset($this->intermediateСertificates[$certificate->getAuthorityKeyIdentifier()])) {
            return new Verification(Verification::CRT_INTERMEDIATE_NOT_FOUND, null, $certificate);
        }

        $issuerCertificate = $this->intermediateСertificates[$certificate->getAuthorityKeyIdentifier()];
        $issuerPEM = PEMConverter::toPEM($issuerCertificate->getPublicKey());
        // TODO: fix double line brake in main library
        $issuerPEM = preg_replace("/\r\n\r\n/", "\r\n", $issuerPEM);
        $issuerPublicKey = openssl_pkey_get_public($issuerPEM);

        // 1. Verify digital signature of x509 certificate against an issuer's public key
        $sslData = $certificate->getTBSCertificate()->getBinary();
        $signature = $certificate->getSignatureValue();
        $hashAlgorithm = AlgorithmEncryption::byOid($certificate->getSignatureAlgorithm()->getAlgorithmOid());
        $verify = openssl_verify($sslData, $signature, $issuerPublicKey, $hashAlgorithm);

        if ($verify !== 1) {
            return new Verification(Verification::CRT_NOT_VALID_SIGNATURE, false, $certificate);
        }

        // 2. Verify issue date
        $verify = $issuerCertificate->verifyDate($subjectDate);
        if ($verify->isVerified() !== true) {
            return $verify;
        }

        return new Verification("Certificate chain verified", true);
    }
}
