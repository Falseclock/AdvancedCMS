<?php
/**
 * TimeStampRequest
 * @see \Adapik\CMS\TimeStampRequest
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/CMS-EDITOR
 */

namespace Falseclock\EditCMS;

use Adapik\CMS\Algorithm;
use Adapik\CMS\Exception\FormatException;
use Exception;
use FG\ASN1\Universal\Boolean;
use FG\ASN1\Universal\Integer;
use FG\ASN1\Universal\NullObject;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\OctetString;
use FG\ASN1\Universal\Sequence;

/**
 * Class TimeStampRequest
 *
 * @see     \Adapik\CMS\Maps\TimeStampRequest
 * @package Adapik\CMS
 */
class TimeStampRequest extends \Adapik\CMS\TimeStampRequest implements Request
{

    /**
     * @param OctetString $data data which should be queried with TS request
     * @param string $hashAlgorithmOID
     * @return TimeStampRequest
     * @throws Exception
     */
    public static function createSimple(OctetString $data, string $hashAlgorithmOID = Algorithm::OID_SHA256)
    {
        $tspRequest = Sequence::create([
            # version
            Integer::create(1),
            # messageImprint
            Sequence::create([
                Sequence::create([
                    ObjectIdentifier::create($hashAlgorithmOID),
                    NullObject::create(),
                ]),
                OctetString::createFromString(Algorithm::hashValue($hashAlgorithmOID, $data->getBinaryContent()))
            ]),
            # nonce
            Integer::create(rand() << 32 | rand()),
            # certReq
            Boolean::create(true),
        ]);

        return new self($tspRequest);
    }


    /**
     * @param string[] $urls
     * @return TimeStampResponse|null
     * @throws FormatException
     */
    public function processRequest(array $urls)
    {

    }
}