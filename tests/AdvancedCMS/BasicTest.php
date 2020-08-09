<?php

namespace Falseclock\Test\EditCMS;

use Falseclock\AdvancedCMS\EncapsulatedContentInfo;
use Falseclock\AdvancedCMS\SignedData;
use Falseclock\AdvancedCMS\SignedDataContent;
use Falseclock\AdvancedCMS\SignerInfo;
use Falseclock\AdvancedCMS\UnsignedAttributes;
use FG\ASN1\Universal\OctetString;
use FG\ASN1\Universal\Sequence;
use PHPUnit\Framework\TestCase;

class BasicTest extends TestCase
{
    public function testInstance()
    {
        $signedData = SignedData::createFromContent($this->getFull());
        self::assertInstanceOf(SignedData::class, $signedData);

        $binary = base64_decode($this->getFull());
        $sequence = Sequence::fromBinary($binary);

        $signedData = new SignedData($sequence);
        $signerInfo = $signedData->getSignedDataContent()->getSignerInfoSet()[0];

        self::assertInstanceOf(EncapsulatedContentInfo::class, $signedData->getSignedDataContent()->getEncapsulatedContentInfo());
        self::assertInstanceOf(SignedData::class, $signedData);
        self::assertInstanceOf(SignedDataContent::class, $signedData->getSignedDataContent());
        self::assertInstanceOf(SignerInfo::class, $signerInfo);
        self::assertInstanceOf(UnsignedAttributes::class, $signerInfo->getUnsignedAttributes());
    }

    public function testEncapsulatedContentInfo()
    {
        $binary = base64_decode($this->getFull());
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



    private function getFull()
    {
        return file_get_contents(__DIR__ . '/../fixtures/full.cms');
    }

    private function getNoDataNoUnsigned()
    {
        return file_get_contents(__DIR__ . '/../fixtures/noDataNoUnsigned.cms');
    }

    private function getWithDataNoUnsigned()
    {
        return file_get_contents(__DIR__ . '/../fixtures/withDataNoUnsigned.cms');
    }
}
