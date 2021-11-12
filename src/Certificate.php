<?php
/**
 * Certificate
 * @see \Adapik\CMS\Certificate
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/AdvancedCMS
 */

declare(strict_types=1);

namespace Falseclock\AdvancedCMS;

class Certificate extends \Adapik\CMS\Certificate
{
    public function verify(): bool {
        return false;
    }
}
