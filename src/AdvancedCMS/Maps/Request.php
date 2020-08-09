<?php
/**
 * Request
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/CMS-EDITOR
 */

namespace Falseclock\AdvancedCMS\Maps;

use Adapik\CMS\Maps\CertID;
use Adapik\CMS\Maps\Extensions;
use FG\ASN1\Identifier;

abstract class Request
{
    /**
     * Request ::=     SEQUENCE {
     *        reqCert                    CertID,
     *        singleRequestExtensions    [0] EXPLICIT Extensions OPTIONAL
     * }
     */
    const MAP = [
        'type' => Identifier::SEQUENCE,
        'children' => [
            'reqCert' => CertID::MAP,
            'singleRequestExtensions' => [
                    'constant' => 0,
                    'optional' => true,
                    'explicit' => true,
                ] + Extensions::MAP,
        ],
    ];
}
