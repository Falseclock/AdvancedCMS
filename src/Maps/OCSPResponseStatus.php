<?php
/**
 * OCSPResponseStatus
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/AdvancedCMS
 */

namespace Falseclock\AdvancedCMS\Maps;

use FG\ASN1\Identifier;

abstract class OCSPResponseStatus
{
    /**
     * OCSPResponseStatus ::= ENUMERATED {
     *        successful            (0),      --Response has valid confirmations
     *        malformedRequest      (1),      --Illegal confirmation request
     *        internalError         (2),      --Internal error in issuer
     *        tryLater              (3),      --Try again later
     *        --(4) is not used
     *        sigRequired           (5),      --Must sign the request
     *        unauthorized          (6)       --Request unauthorized
     * }
     */
    const MAP = [
        'type' => Identifier::ENUMERATED,
        'mapping' => [
            0 => 'successful',
            1 => 'malformedRequest',
            2 => 'internalError',
            3 => 'tryLater',
            // --(4) is not used
            5 => 'sigRequired',
            6 => 'unauthorized',
        ],
    ];
}
