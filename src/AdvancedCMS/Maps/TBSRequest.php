<?php
/**
 * TBSRequest
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/AdvancedCMS
 */

namespace Falseclock\AdvancedCMS\Maps;

use Adapik\CMS\Maps\Extensions;
use Adapik\CMS\Maps\GeneralName;
use FG\ASN1\Identifier;

abstract class TBSRequest
{
    /**
     * TBSRequest      ::=     SEQUENCE {
     *        version             [0] EXPLICIT Version DEFAULT v1,
     *        requestorName       [1] EXPLICIT GeneralName OPTIONAL,
     *        requestList             SEQUENCE OF Request,
     *        requestExtensions   [2] EXPLICIT Extensions OPTIONAL
     * }
     */

    const MAP = [
        'type' => Identifier::SEQUENCE,
        'children' => [
            'version' => [
                'type' => Identifier::INTEGER,
                'constant' => 0,
                'optional' => true,
                'explicit' => true,
                'mapping' => ['v1', 'v2', 'v3'],
                'default' => 'v1',
            ],
            'requestorName' => [
                    'constant' => 1,
                    'optional' => true,
                    'explicit' => true,
                ] + GeneralName::MAP,
            'requestList' => [
                'type' => Identifier::SEQUENCE,
                'min' => 1,
                'max' => -1,
                'children' => Request::MAP,
            ],
            'requestExtensions' => [
                    'constant' => 2,
                    'optional' => true,
                    'explicit' => true,
                ] + Extensions::MAP,
        ],
    ];
}
