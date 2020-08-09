<?php
declare(strict_types=1);

namespace Falseclock\AdvancedCMS\Test;

use Adapik\CMS\UnsignedAttribute;
use Exception;
use Falseclock\AdvancedCMS\OCSPResponse;
use Falseclock\AdvancedCMS\RevocationValues;
use Falseclock\AdvancedCMS\SignedData;
use Falseclock\AdvancedCMS\SignedDataContent;
use Falseclock\AdvancedCMS\SignerInfo;
use Falseclock\AdvancedCMS\TimeStampResponse;
use Falseclock\AdvancedCMS\TimeStampToken;
use Falseclock\AdvancedCMS\UnsignedAttributes;
use FG\ASN1\Universal\Sequence;

class SignerInfoTest extends MainTest
{
    public function testNoUnsigned()
    {
        $signedData = SignedData::createFromContent($this->getWithDataNoUnsignedCMS());
        self::assertInstanceOf(SignedData::class, $signedData);

        $signedDataContent = $signedData->getSignedDataContent();
        self::assertInstanceOf(SignedDataContent::class, $signedDataContent);

        foreach ($signedDataContent->getSignerInfoSet() as $signerInfo) {
            self::assertInstanceOf(SignerInfo::class, $signerInfo);

            $unsignedAttributes = $signerInfo->getUnsignedAttributes();

            self::assertNull($unsignedAttributes);
        }
    }

    public function testUnsigned()
    {
        $signedData = SignedData::createFromContent($this->getSetOfUnsignedCMS());

        foreach ($signedData->getSignedDataContent()->getSignerInfoSet() as $signerInfo) {
            $unsignedAttributes = $signerInfo->getUnsignedAttributes();

            self::assertNotNull($unsignedAttributes);

            foreach ($unsignedAttributes->getAttributes() as $unsignedAttribute) {
                self::assertInstanceOf(UnsignedAttribute::class, $unsignedAttribute);

                $binary = $unsignedAttribute->getBinary();

                // just test no exception
                new UnsignedAttribute(Sequence::fromBinary($binary));
            }

            self::assertInstanceOf(RevocationValues::class, $unsignedAttributes->getRevocationValues());
            self::assertInstanceOf(TimeStampToken::class, $unsignedAttributes->getTimeStampToken());
        }
    }

    public function testReplacement()
    {
        $signedData = SignedData::createFromContent($this->getSetOfUnsignedCMS());

        $OCSPResponse = OCSPResponse::createFromContent($this->getOCSPResponse());
        self::assertInstanceOf(OCSPResponse::class, $OCSPResponse);

        $TimeStampResponse = TimeStampResponse::createFromContent($this->getTimeStampResponse());
        self::assertInstanceOf(TimeStampResponse::class, $TimeStampResponse);

        foreach ($signedData->getSignedDataContent()->getSignerInfoSet() as $signerInfo) {
            $unsignedAttributes = $signerInfo->getUnsignedAttributes();

            self::assertNotNull($unsignedAttributes->getRevocationValues());
            self::assertInstanceOf(RevocationValues::class, $unsignedAttributes->getRevocationValues());

            $unsignedAttributes->setRevocationValues($OCSPResponse->getBasicOCSPResponse());
            $unsignedAttributes->setTimeStampToken($TimeStampResponse);

            // Create again
            $binary = $unsignedAttributes->getBinary();
            $unsignedAttributes = new UnsignedAttributes(Sequence::fromBinary($binary));

            self::assertEquals($OCSPResponse->getBasicOCSPResponse()->getBinary(), $unsignedAttributes->getRevocationValues()->getBasicOCSPResponse()->getBinary());
            self::assertEquals($TimeStampResponse->getSignedData()->getBinary(), $unsignedAttributes->getTimeStampToken()->getSignedData()->getBinary());

            $this->expectException(Exception::class);
            $unsignedAttributes->setRevocationValues();
        }
    }

    public function testAppend()
    {
        $signedData = SignedData::createFromContent($this->getNoDataNoUnsignedCMS());

        $OCSPResponse = OCSPResponse::createFromContent($this->getOCSPResponse());
        self::assertInstanceOf(OCSPResponse::class, $OCSPResponse);

        $TimeStampResponse = TimeStampResponse::createFromContent($this->getTimeStampResponse());
        self::assertInstanceOf(TimeStampResponse::class, $TimeStampResponse);

        foreach ($signedData->getSignedDataContent()->getSignerInfoSet() as $signerInfo) {
            $unsignedAttributes = $signerInfo->getUnsignedAttributes();
            self::assertNull($unsignedAttributes);

            $TimeStampToken = TimeStampToken::createFromTimeStampResponse($TimeStampResponse);
            $TimeStampToken->getIdentifier();
            $TimeStampToken->getTSTInfo();
            $TimeStampToken->getSignedData();
            $TimeStampToken->getValue();

            $signerInfo->addUnsignedAttribute($TimeStampToken);

            $RevocationValues = RevocationValues::createFromOCSPResponse($OCSPResponse->getBasicOCSPResponse());
            $signerInfo->addUnsignedAttribute($RevocationValues);

            $unsignedAttributes = $signerInfo->getUnsignedAttributes();

            // Create again
            $binary = $unsignedAttributes->getBinary();
            $unsignedAttributes = new UnsignedAttributes(Sequence::fromBinary($binary));
            self::assertEquals($OCSPResponse->getBasicOCSPResponse()->getBinary(), $unsignedAttributes->getRevocationValues()->getBasicOCSPResponse()->getBinary());
            self::assertEquals($TimeStampResponse->getSignedData()->getBinary(), $unsignedAttributes->getTimeStampToken()->getSignedData()->getBinary());
        }
    }
}
