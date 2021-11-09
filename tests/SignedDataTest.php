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
use Falseclock\AdvancedCMS\EncapsulatedContentInfo;
use Falseclock\AdvancedCMS\SignedData;
use Falseclock\AdvancedCMS\SignedDataContent;
use Falseclock\AdvancedCMS\SignerInfo;
use Falseclock\AdvancedCMS\UnsignedAttributes;
use FG\ASN1\Exception\ParserException;
use FG\ASN1\Universal\Sequence;

class SignedDataTest extends MainTest
{
    /**
     * @throws ParserException
     * @throws FormatException
     * @throws Exception
     */
    public function testInstances()
    {
        // test from content
        $signedData = SignedData::createFromContent($this->getFullCMS());
        self::assertInstanceOf(SignedData::class, $signedData);

        // test from Sequence
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

    /**
     * @throws FormatException
     * @throws Exception
     */
    public function testMerge()
    {
        $signedData0 = SignedData::createFromContent(base64_decode($this->getNoDataNoUnsignedCMS()));
        $signedData1 = SignedData::createFromContent(base64_decode($this->getNoDataNoUnsignedCMS()));
        $signedData2 = SignedData::createFromContent(base64_decode($this->getWithDataNoUnsignedCMS()));

        $signedData1->mergeCMS($signedData2);

        // Testing creation from binary
        $signedData3 = SignedData::createFromContent($signedData1->getBinary());

        self::assertCount(
            count($signedData0->getSignedDataContent()->getCertificateSet()) + count($signedData2->getSignedDataContent()->getCertificateSet()),
            $signedData3->getSignedDataContent()->getCertificateSet());

        self::assertCount(
            count($signedData0->getSignedDataContent()->getSignerInfoSet()) + count($signedData2->getSignedDataContent()->getSignerInfoSet()),
            $signedData3->getSignedDataContent()->getSignerInfoSet());
    }

    /**
     * @throws FormatException
     * @throws Exception
     */
    public function testVerify()
    {
        $signedData = SignedData::createFromContent(base64_decode($this->DoubleSignOCSPAndTSPAndData()));

        $signedData->verify();
    }
}
