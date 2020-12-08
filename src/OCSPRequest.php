<?php
/**
 * OCSPRequest
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
use Exception;
use FG\ASN1\Exception\ParserException;
use FG\ASN1\ExplicitlyTaggedObject;
use FG\ASN1\Universal\BitString;
use FG\ASN1\Universal\Integer;
use FG\ASN1\Universal\NullObject;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\OctetString;
use FG\ASN1\Universal\Sequence;

/**
 * Class OCSPRequest
 *
 * @see     Maps\OCSPRequest
 * @package Falseclock\AdvancedCMS
 */
class OCSPRequest extends CMSBase
{
    const CONTENT_TYPE = 'application/ocsp-request';
    const OCSP_DEFAULT_NONCE_LENGTH = 16;
    const OID_OCSPNonce = '1.3.6.1.5.5.7.48.1.2';

    /**
     * @var Sequence
     */
    protected $object;

    /**
     * @param string $content
     * @return OCSPRequest
     * @throws FormatException
     */
    public static function createFromContent(string $content): CMSBase
    {
        return new self(self::makeFromContent($content, Maps\OCSPRequest::class, Sequence::class));
    }

    /**
     * @param Certificate $publicCertificate
     * @param Certificate $intermediateCertificate
     * @param string $hashAlgorithmOID
     *
     * @return OCSPRequest
     * @throws FormatException
     * @throws ParserException
     */
    public static function createSimple(Certificate $publicCertificate, Certificate $intermediateCertificate, string $hashAlgorithmOID = Algorithm::OID_SHA1): self
    {
        /** @see Maps\TBSRequest */
        $tbsRequest = Sequence::create([
                // потомки TBSRequest
                # -- version
                # -- requestorName
                # requestList
                Sequence::create([
                        /** @see Request */
                        Sequence::create([
                                // потомки Request
                                # reqCert
                                /** @see CertID */
                                Sequence::create([
                                        // потомки  CertID
                                        # hashAlgorithm
                                        /** @see AlgorithmIdentifier */
                                        Sequence::create([
                                                // потомки AlgorithmIdentifier
                                                # algorithm
                                                ObjectIdentifier::create($hashAlgorithmOID),
                                                # parameters
                                                NullObject::create()
                                            ]
                                        ),
                                        # issuerNameHash
                                        OctetString::createFromString(self::getNameHash($hashAlgorithmOID, $intermediateCertificate)),
                                        # issuerKeyHash
                                        OctetString::createFromString(self::getKeyHash($hashAlgorithmOID, $intermediateCertificate)),                                        # serialNumber
                                        Integer::create($publicCertificate->getSerial())
                                    ]
                                )
                                # singleRequestExtensions
                            ]
                        )
                    ]
                ),
                # requestExtensions
                ExplicitlyTaggedObject::create(2,
                    Sequence::create([
                            Sequence::create([
                                    ObjectIdentifier::create(self::OID_OCSPNonce),
                                    OctetString::createFromString(OctetString::createFromString((string)self::generateNonce())->getBinary())
                                ]
                            )
                        ]
                    )
                )
            ]
        );

        return new self(Sequence::create([$tbsRequest/*, $optionalSignature*/]));
    }

    /**
     * @param int|null $length
     * @return string
     * @throws Exception
     */
    protected static function generateNonce(int $length = null): string
    {
        return random_bytes($length ?? self::OCSP_DEFAULT_NONCE_LENGTH);
    }

    /**
     * @return TBSRequest
     */
    public function getTBSRequest(): TBSRequest
    {
        return new TBSRequest($this->object->getChildren()[0]);
    }

    /**
     * @return Signature|null
     * @throws ParserException
     */
    public function getOptionalSignature(): ?Signature
    {
        $children = $this->object->getChildren();

        if (count($children) == 2) {
            $binary = $children[1]->getBinaryContent();
            return new Signature(Sequence::fromBinary($binary));
        }

        return null;
    }

    /**
     * @param string $algorithmOID
     * @param Certificate $certificate
     * @return string
     * @throws FormatException
     * @throws ParserException
     */
    private static function getNameHash(string $algorithmOID, Certificate $certificate): string
    {
        $binary = $certificate->getBinary();
        /** @var Sequence $certificate */
        $certificate = Sequence::fromBinary($binary);
        return Algorithm::hashValue($algorithmOID, self::_getTBSCertificate($certificate)->getChildren()[5]->getBinary());
    }

    /**
     * @param string $algorithmOID
     * @param Certificate $certificate
     * @return string
     * @throws FormatException
     * @throws ParserException
     */
    private static function getKeyHash(string $algorithmOID, Certificate $certificate): string
    {
        $binary = $certificate->getBinary();
        /** @var Sequence $certificate */
        $certificate = Sequence::fromBinary($binary);
        $child = self::_getTBSCertificate($certificate)->getChildren()[6];
        /** @var BitString $octet */
        $octet = $child->findChildrenByType(BitString::class)[0];

        return Algorithm::hashValue($algorithmOID, hex2bin($octet->getStringValue()));
    }

    /**
     * @param Sequence $certificate
     * @return Sequence
     * @throws Exception
     */
    private static function _getTBSCertificate(Sequence $certificate): Sequence
    {
        return $certificate->findChildrenByType(Sequence::class)[0];
    }
}
