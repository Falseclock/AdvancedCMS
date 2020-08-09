<?php
/**
 * TimeStampRequest
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/AdvancedCMS
 */

namespace Falseclock\AdvancedCMS\Maps;

use Adapik\CMS\Maps\Extensions;
use Adapik\CMS\Maps\MessageImprint;
use FG\ASN1\Identifier;

abstract class TimeStampRequest
{
    /**
     * TimeStampReq ::= SEQUENCE  {
     *        version                  INTEGER  { v1(1) },
     *        messageImprint           MessageImprint,
     *            --a hash algorithm OID and the hash value of the data to be
     *            --time-stamped
     *        reqPolicy                TSAPolicyId                OPTIONAL,
     *        nonce                    INTEGER                    OPTIONAL,
     *        certReq                  BOOLEAN                    DEFAULT FALSE,
     *        extensions               [0] IMPLICIT Extensions    OPTIONAL
     * }
     */
    const MAP = [
        'type' => Identifier::SEQUENCE,
        'children' => [
            'version' => [
                'type' => Identifier::INTEGER,
                'mapping' => ['v1', 'v2', 'v3'],
                'default' => 'v1',
            ],
            'messageImprint' => MessageImprint::MAP,
            'reqPolicy' => [
                'type' => Identifier::OBJECT_IDENTIFIER,
                'optional' => true,
            ],
            'nonce' => [
                'type' => Identifier::INTEGER,
                'optional' => true,
            ],
            'certReq' => [
                'type' => Identifier::BOOLEAN,
                'default' => true,
            ],
            'extensions' => [
                    'constant' => 0,
                    'optional' => true,
                    'implicit' => true,
                ] + Extensions::MAP,
        ],
    ];
}
