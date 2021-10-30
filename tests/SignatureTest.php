<?php
/**
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/AdvancedCMS
 */

declare(strict_types=1);

namespace Falseclock\AdvancedCMS\Test;

use Falseclock\AdvancedCMS\OCSPRequest;
use Falseclock\AdvancedCMS\Signature;

class SignatureTest extends MainTest
{
    public function testCreation()
    {
        $OCSPRequest = OCSPRequest::createFromContent($this->getOCSPRequestWithSignature());

        $optionalSignature = $OCSPRequest->getOptionalSignature();

        $binary = $optionalSignature->getBinary();
        $newSignature = Signature::createFromContent($binary);
        self::assertEquals($binary, $newSignature->getBinary());
    }
}
