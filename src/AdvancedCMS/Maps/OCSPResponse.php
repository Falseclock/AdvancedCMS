<?php
/**
 * OCSPResponse
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/CMS-EDITOR
 */

namespace Falseclock\AdvancedCMS\Maps;

use FG\ASN1\Identifier;

abstract class OCSPResponse
{
    /**
     * OCSPResponse ::= SEQUENCE {
     *        responseStatus         OCSPResponseStatus,
     *        responseBytes          [0] EXPLICIT ResponseBytes OPTIONAL
     * }
     */

    const MAP = [
        'type' => Identifier::SEQUENCE,
        'children' => [
            'responseStatus' => OCSPResponseStatus::MAP,
            'responseBytes' => [
                    'optional' => true,
                    'explicit' => true,
                    'constant' => 0,
                ] + ResponseBytes::MAP,
        ],
    ];
}
