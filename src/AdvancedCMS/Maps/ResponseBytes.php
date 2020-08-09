<?php
/**
 * ResponseBytes
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/CMS-EDITOR
 */

namespace Falseclock\AdvancedCMS\Maps;

use Adapik\CMS\Maps\BasicOCSPResponse;
use FG\ASN1\Identifier;

abstract class ResponseBytes
{
    /**
     * ResponseBytes ::= SEQUENCE {
     *        responseType   OBJECT IDENTIFIER,
     *        response       OCTET STRING }
     */
    const MAP = [
        'type' => Identifier::SEQUENCE,
        'children' => [
            'responseType' => ['type' => Identifier::OBJECT_IDENTIFIER],
            'response' => ['type' => Identifier::OCTETSTRING, 'children' => BasicOCSPResponse::MAP],
        ],
    ];
}
