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
use Adapik\CMS\Exception\FormatException;
use Adapik\CMS\Signature;
use Exception;
use FG\ASN1\ExplicitlyTaggedObject;
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
class OCSPRequest extends Request
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
    public static function createFromContent(string $content)
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
     */
    public static function createSimple(Certificate $publicCertificate, Certificate $intermediateCertificate, string $hashAlgorithmOID = Algorithm::OID_SHA1)
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
                                        OctetString::createFromString($intermediateCertificate->getNameHash($hashAlgorithmOID)),
                                        # issuerKeyHash
                                        OctetString::createFromString($intermediateCertificate->getKeyHash($hashAlgorithmOID)),
                                        # serialNumber
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
        // TODO: signature create and test
        return new self(Sequence::create([$tbsRequest/*, $optionalSignature*/]));
    }

    /**
     * @param int|null $length
     * @return string
     * @throws Exception
     */
    private static function generateNonce(int $length = null)
    {
        return random_bytes($length ?? self::OCSP_DEFAULT_NONCE_LENGTH);
    }

    /**
     * @return TBSRequest
     */
    public function getTBSRequest()
    {
        return new TBSRequest($this->object->getChildren()[0]);
    }

    /**
     * FIXME: возвращать объект
     * @return Signature|null
     * @throws FormatException
     */
    public function getOptionalSignature()
    {
        $children = $this->object->getChildren();

        if (count($children) == 2) {
            return Signature::createFromContent($children[1]->getBinaryContent());
        }

        return null;
    }
}
