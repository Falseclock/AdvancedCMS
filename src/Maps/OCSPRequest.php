<?php
/**
 * OCSPRequest
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/AdvancedCMS
 */

namespace Falseclock\AdvancedCMS\Maps;

use FG\ASN1\Identifier;

abstract class OCSPRequest
{
    /**
     * OCSPRequest     ::=     SEQUENCE {
     *        tbsRequest                  TBSRequest,
     *        optionalSignature   [0]     EXPLICIT Signature OPTIONAL
     * }
     */

    const MAP = [
        'type' => Identifier::SEQUENCE,
        'children' => [
            'tbsRequest' => TBSRequest::MAP,
            'optionalSignature' => Signature::MAP + [
                    'constant' => 0,
                    'explicit' => true,
                    'optional' => true,
                ],
        ],
    ];
}
