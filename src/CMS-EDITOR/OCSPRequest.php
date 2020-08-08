<?php
/**
 * OCSPRequest
 * @see \Adapik\CMS\OCSPRequest
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/CMS-EDITOR
 */

namespace Falseclock\EditCMS;

use Adapik\CMS\Algorithm;
use Adapik\CMS\Certificate;
use Adapik\CMS\Exception\FormatException;
use Adapik\CMS\OCSPResponse;
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
 * @see     \Adapik\CMS\Maps\OCSPRequest
 * @package Adapik\CMS
 */
class OCSPRequest extends \Adapik\CMS\OCSPRequest
{
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
        /** @see \Adapik\CMS\Maps\TBSRequest */
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
                                                NullObject::create(),
                                            ]
                                        ),
                                        # issuerNameHash
                                        OctetString::createFromString($intermediateCertificate->getNameHash($hashAlgorithmOID)),
                                        # issuerKeyHash
                                        OctetString::createFromString($intermediateCertificate->getKeyHash($hashAlgorithmOID)),
                                        # serialNumber
                                        Integer::create($publicCertificate->getSerial()),
                                    ]
                                ),
                                # singleRequestExtensions
                            ]
                        ),
                    ]
                ),
                # requestExtensions
                ExplicitlyTaggedObject::create(2,
                    Sequence::create([
                            Sequence::create([
                                ObjectIdentifier::create(\Adapik\CMS\OCSPRequest::OID_OCSPNonce),
                                OctetString::createFromString(OctetString::createFromString((string)self::generateNonce())->getBinary()),
                            ],
                            ),
                        ]
                    ),
                ),
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
     * @param string[] $urls
     * @param int $timeOut
     * @return OCSPResponse|null
     * @throws FormatException
     */
    public function processRequest(array $urls, int $timeOut = 5)
    {
        $this->processErrors = [];

        foreach ($urls as $url) {

            $result = $this->curlRequest($url, $this->object->getBinary(), self::CONTENT_TYPE, OCSPResponse::CONTENT_TYPE);

            // Actually we need only 1 response, and if array is not set - we do not have any errors
            if (!isset($this->processErrors[$url]) && !is_null($result)) {
                return OCSPResponse::createFromContent($result);
            }
        }

        return null;
    }
}