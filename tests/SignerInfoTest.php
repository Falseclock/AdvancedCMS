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
use FG\ASN1\Exception\ParserException;
use FG\ASN1\Universal\Sequence;

class SignerInfoTest extends MainTest
{
    /**
     * For test purposes
     *
     * @throws FormatException
     * @throws \FG\ASN1\Exception\Exception
     * @throws ParserException
     */
    public function testForBaseFork()
    {
        $signedData = SignedData::createFromContent($this->getWithDataNoUnsignedCMS());

        $TimeStampResponse = TimeStampResponse::createFromContent($this->getTimeStampResponseWithAccuracy());
        $TimeStampToken = TimeStampToken::createFromTimeStampResponse($TimeStampResponse);

        foreach ($signedData->getSignedDataContent()->getSignerInfoSet() as $signerInfo) {
            $signerInfo->addUnsignedAttribute($TimeStampToken);
        }

        self::assertEquals(1, 1);


        $OCSPResponse = OCSPResponse::createFromContent(base64_decode("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"
            )
        );

        $OCSPResponse->getBasicOCSPResponse();
    }

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

    public function testCreateFromContent()
    {
        $signedData = SignedData::createFromContent($this->getWithDataNoUnsignedCMS());
        foreach ($signedData->getSignedDataContent()->getSignerInfoSet() as $signerInfo) {
            $binary = $signerInfo->getBinary();
            $value = SignerInfo::createFromContent($binary);
            self::assertInstanceOf(SignerInfo::class, $value);
            self::assertEquals($value->getBinary(), $binary);
        }
    }

    public function testReplaceAttribute()
    {
        $signedData = SignedData::createFromContent($this->getWithDataNoUnsignedCMS());
        self::assertInstanceOf(SignedData::class, $signedData);

        $signedDataContent = $signedData->getSignedDataContent();
        self::assertInstanceOf(SignedDataContent::class, $signedDataContent);

        foreach ($signedDataContent->getSignerInfoSet() as $signerInfo) {
            self::assertInstanceOf(SignerInfo::class, $signerInfo);

            $unsignedAttributes = $signerInfo->getUnsignedAttributes();

            self::assertNull($unsignedAttributes);

            $TimeStampResponse = TimeStampResponse::createFromContent($this->getTimeStampResponse());
            $TimeStampToken = TimeStampToken::createFromTimeStampResponse($TimeStampResponse);
            $signerInfo->addUnsignedAttribute($TimeStampToken);
            $signerInfo->addUnsignedAttribute($TimeStampToken);

            $unsignedAttributes = $signerInfo->getUnsignedAttributes();
            $OCSPResponse = OCSPResponse::createFromContent($this->getOCSPResponse());
            $unsignedAttributes->setRevocationValues($OCSPResponse->getBasicOCSPResponse());

            $signerInfo->deleteUnsignedAttributes();

            self::assertNull($signerInfo->getUnsignedAttributes());

            $RevocationValues = RevocationValues::createFromOCSPResponse($OCSPResponse->getBasicOCSPResponse());
            $signerInfo->addUnsignedAttribute($RevocationValues);
            $unsignedAttributes = $signerInfo->getUnsignedAttributes();

            self::assertNotNull($signerInfo->getUnsignedAttributes());

            $unsignedAttributes->setTimeStampToken($TimeStampResponse);

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

                $unsignedAttributes->appendAttribute($unsignedAttribute);
                $unsignedAttributes->replaceAttribute($unsignedAttribute->getIdentifier()->__toString(), $unsignedAttribute);

            }

            self::assertInstanceOf(RevocationValues::class, $unsignedAttributes->getRevocationValues());
            self::assertInstanceOf(TimeStampToken::class, $unsignedAttributes->getTimeStampToken());

            $signerInfo->deleteUnsignedAttributes();

            self::assertNull($signerInfo->getUnsignedAttributes());
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
