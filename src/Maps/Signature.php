<?php
/**
 * Signature
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/AdvancedCMS
 */

namespace Falseclock\AdvancedCMS\Maps;

use Adapik\CMS\Maps\AlgorithmIdentifier;
use Adapik\CMS\Maps\Certificate;
use FG\ASN1\Identifier;

abstract class Signature
{
    /**
     * Signature       ::=     SEQUENCE {
     *        signatureAlgorithm   AlgorithmIdentifier,
     *        signature            BIT STRING,
     *        certs                [0] EXPLICIT SEQUENCE OF Certificate OPTIONAL
     * }
     */
    const MAP = [
        'type' => Identifier::SEQUENCE,
        'children' => [
            'signatureAlgorithm' => AlgorithmIdentifier::MAP,
            'signature' => ['type' => Identifier::BITSTRING],
            'certs' => [
                'constant' => 0,
                'explicit' => true,
                'optional' => true,
                'min' => 1,
                'max' => -1,
                'type' => Identifier::SEQUENCE,
                'children' => Certificate::MAP
            ],
        ],
    ];
}
