<?php
/**
 * EncapsulatedContentInfo
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/AdvancedCMS
 */

namespace Falseclock\AdvancedCMS;

use Adapik\CMS\Algorithm;
use Adapik\CMS\Certificate;
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

class Template
{
    /**
     * @param Certificate $publicCertificate
     * @param Certificate $intermediateCertificate
     * @param string $hashAlgorithmOID
     * @return OCSPRequest
     * @throws FormatException
     * @throws ParserException
     */
    public static function OCSPRequest(Certificate $publicCertificate, Certificate $intermediateCertificate, string $hashAlgorithmOID = Algorithm::OID_SHA1): OCSPRequest
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
                                    ObjectIdentifier::create(OCSPRequest::OID_OCSPNonce),
                                    OctetString::createFromString(OctetString::createFromString((string)self::generateNonce())->getBinary())
                                ]
                            )
                        ]
                    )
                )
            ]
        );

        return new OCSPRequest(Sequence::create([$tbsRequest/*, $optionalSignature*/]));
    }


    /**
     * @param int|null $length
     * @return string
     * @throws Exception
     */
    private static function generateNonce(int $length = null): string
    {
        return random_bytes($length ?? OCSPRequest::OCSP_DEFAULT_NONCE_LENGTH);
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
    public static function getKeyHash(string $algorithmOID, Certificate $certificate): string
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
