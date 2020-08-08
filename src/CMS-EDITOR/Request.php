<?php
/**
 * Request
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Adapik/CMS
 */

namespace Falseclock\EditCMS;

use FG\ASN1\ASN1Object;

interface Request
{
    /**
     * @param string[] $urls
     * @return ASN1Object|null
     */
    public function processRequest(array $urls);
}
