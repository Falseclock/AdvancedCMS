<?php

namespace Falseclock\AdvancedCMS\Test;

use Falseclock\AdvancedCMS\EncapsulatedContentInfo;
use Falseclock\AdvancedCMS\SignedData;
use Falseclock\AdvancedCMS\SignedDataContent;
use Falseclock\AdvancedCMS\SignerInfo;
use Falseclock\AdvancedCMS\UnsignedAttributes;
use FG\ASN1\Universal\Sequence;

class InstanceOfTest extends MainTest
{
    public function testInstance()
    {
        $signedData = SignedData::createFromContent($this->getFullCMS());
        self::assertInstanceOf(SignedData::class, $signedData);

        $binary = base64_decode($this->getFullCMS());
        $sequence = Sequence::fromBinary($binary);

        $signedData = new SignedData($sequence);
        $signerInfo = $signedData->getSignedDataContent()->getSignerInfoSet()[0];

        self::assertInstanceOf(EncapsulatedContentInfo::class, $signedData->getSignedDataContent()->getEncapsulatedContentInfo());
        self::assertInstanceOf(SignedData::class, $signedData);
        self::assertInstanceOf(SignedDataContent::class, $signedData->getSignedDataContent());
        self::assertInstanceOf(SignerInfo::class, $signerInfo);
        self::assertInstanceOf(UnsignedAttributes::class, $signerInfo->getUnsignedAttributes());
    }
}
