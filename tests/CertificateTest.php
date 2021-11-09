<?php
/**
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/AdvancedCMS
 */

declare(strict_types=1);

namespace Falseclock\AdvancedCMS\Test;

use Adapik\CMS\Exception\FormatException;
use Exception;
use Falseclock\AdvancedCMS\SignedData;

class CertificateTest extends MainTest
{
    /**
     * @throws FormatException
     * @throws Exception
     */
    public function testSubject() {
        $signedData = SignedData::createFromContent($this->getFullCMS());
        $certificates = $signedData->getSignedDataContent()->getCertificateSet();

        foreach ($certificates as $certificate) {
            echo $certificate->getSubject()->getSurname();
            echo $certificate->getSubject()->getGivenName();
            echo $certificate->getSubject()->getCommonName();
        }
    }
}