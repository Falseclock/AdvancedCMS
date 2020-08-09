<?php
declare(strict_types=1);

namespace Falseclock\AdvancedCMS\Test;

use Falseclock\AdvancedCMS\Maps\OCSPResponseStatus;
use Falseclock\AdvancedCMS\OCSPResponse;

class OCSPResponseStatusTest extends MainTest
{
    public function testMethods()
    {
        $ocspResponse = OCSPResponse::createFromContent($this->getOCSPResponse());
        $responseStatus = $ocspResponse->getResponseStatus();

        $responseStatus->getMapping();
        $responseStatus->isSuccessful();

        self::assertEquals(OCSPResponseStatus::MAP['mapping'], $responseStatus->getMapping());
        self::assertEquals(true, $responseStatus->isSuccessful());
    }
}
