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
use FG\ASN1\Universal\Boolean;
use FG\ASN1\Universal\Integer;
use FG\ASN1\Universal\NullObject;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\OctetString;
use FG\ASN1\Universal\Sequence;

class Template
{
    /**
     * @param OctetString $data
     * @param string $hashAlgorithmOID
     * @return TimeStampRequest
     * @throws FormatException
     */
    public static function TimeStampRequest(OctetString $data, string $hashAlgorithmOID = Algorithm::OID_SHA256): TimeStampRequest
    {
        $tspRequest = Sequence::create([Integer::create(1),Sequence::create([Sequence::create([
                    ObjectIdentifier::create($hashAlgorithmOID),
                    NullObject::create(),
                ]),
                OctetString::createFromString(Algorithm::hashValue($hashAlgorithmOID, $data->getBinaryContent()))
            ]),
            Integer::create(rand() << 32 | rand()),
            Boolean::create(true),
        ]);

        return new TimeStampRequest($tspRequest);
    }

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
        $tbsRequest = Sequence::create([Sequence::create([ Sequence::create([ Sequence::create([Sequence::create([ ObjectIdentifier::create($hashAlgorithmOID),NullObject::create()
                                            ]
                                        ),
                                        OctetString::createFromString(self::getNameHash($hashAlgorithmOID, $intermediateCertificate)),
                                        OctetString::createFromString(self::getKeyHash($hashAlgorithmOID, $intermediateCertificate)),                                        # serialNumber
                                        Integer::create($publicCertificate->getSerial())
                                    ]
                                )
                            ]
                        )
                    ]
                ),
                ExplicitlyTaggedObject::create(2,Sequence::create([ Sequence::create([
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
     * @param Sequence $certificate
     * @return Sequence
     * @throws Exception
     */
    private static function _getTBSCertificate(Sequence $certificate): Sequence
    {
        return $certificate->findChildrenByType(Sequence::class)[0];
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
     * @param int|null $length
     * @return string
     * @throws Exception
     */
    private static function generateNonce(int $length = null): string
    {
        return random_bytes($length ?? OCSPRequest::OCSP_DEFAULT_NONCE_LENGTH);
    }
}
