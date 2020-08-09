<?php

namespace Falseclock\AdvancedCMS\Test;

use Falseclock\AdvancedCMS\SignedData;
use FG\ASN1\Universal\OctetString;
use FG\ASN1\Universal\Sequence;

class EncapsulatedContentInfoTest extends MainTest
{
    public function testEncapsulatedContentInfo()
    {
        $binary = base64_decode($this->getFullCMS());
        $sequence = Sequence::fromBinary($binary);
        $signedData = new SignedData($sequence);
        $signedData->getSignedDataContent()->getEncapsulatedContentInfo()->unSetEContent();

        // Create new signed data from binary
        $newBinary = $signedData->getBinary();
        $newSequence = Sequence::fromBinary($newBinary);
        $newSignedData = new SignedData($newSequence);

        // New data should be unset
        self::assertIsBool($newSignedData->hasData());
        self::assertEquals(false, $newSignedData->hasData());

        $octetString = OctetString::createFromString("new data");
        $newSignedData->getSignedDataContent()->getEncapsulatedContentInfo()->setEContent($octetString);

        $newOneBinary = $newSignedData->getBinary();
        $newOneSequence = Sequence::fromBinary($newOneBinary);
        $newOneSignedData = new SignedData($newOneSequence);

        self::assertIsBool($newOneSignedData->hasData());
        self::assertEquals(true, $newOneSignedData->hasData());

        self::assertEquals(OctetString::createFromString("new data"), $newOneSignedData->getSignedDataContent()->getEncapsulatedContentInfo()->getEContent());
    }
}
