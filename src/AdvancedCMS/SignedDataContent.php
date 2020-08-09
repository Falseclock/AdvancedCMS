<?php
/**
 * SignedDataContent
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/AdvancedCMS
 */

namespace Falseclock\AdvancedCMS;

use Adapik\CMS\AlgorithmIdentifier;
use Adapik\CMS\Certificate;
use Adapik\CMS\RevocationInfoChoices;
use Exception;
use FG\ASN1\Exception\ParserException;
use FG\ASN1\ExplicitlyTaggedObject;
use FG\ASN1\Universal\Sequence;
use FG\ASN1\Universal\Set;

/**
 * Class SignedDataContent
 *
 * @see     \Adapik\CMS\Maps\SignedDataContent
 * @package Falseclock\AdvancedCMS
 */
class SignedDataContent extends \Adapik\CMS\SignedDataContent
{
    /**
     * @param AlgorithmIdentifier $algorithmIdentifier
     * @return SignedDataContent
     * @throws ParserException
     */
    public function appendDigestAlgorithmIdentifier(AlgorithmIdentifier $algorithmIdentifier)
    {
        $binary = $algorithmIdentifier->getBinary();
        $this->object->getChildren()[1]->appendChild(Sequence::fromBinary($binary));

        return $this;
    }

    /**
     * @param Certificate $certificate
     * @return SignedDataContent
     * @throws ParserException
     */
    public function appendCertificate(Certificate $certificate)
    {
        $binary = $certificate->getBinary();
        // FIXME: if getCertificates returns null, cause it is optional field, probably need create
        $this->getTaggedObjectByTagNumber(\Adapik\CMS\Maps\SignedDataContent::CERTIFICATES_TAG_NUMBER)->appendChild(Sequence::fromBinary($binary));

        return $this;
    }

    /**
     * @param RevocationInfoChoices $revocationInfoChoice
     * @return SignedDataContent
     * @todo implement
     */
    public function appendRevocationInfoChoices(RevocationInfoChoices $revocationInfoChoice)
    {
        return $this;
    }

    /**
     * @param SignerInfo $signerInfo
     * @return SignedDataContent
     * @throws ParserException
     */
    public function appendSignerInfo(SignerInfo $signerInfo)
    {
        $signerInfoSet = $this->object->findChildrenByType(Set::class)[1];
        $binary = $signerInfo->getBinary();
        $signerInfoSet->appendChild(Sequence::fromBinary($binary));

        return $this;
    }

    /**
     * @return SignerInfo[]
     * @throws Exception
     */
    public function getSignerInfoSet()
    {
        /** @var SignerInfo[] $children */
        $children = $this->findSignerInfoChildren();

        array_walk($children, function (&$child) {
            $child = new SignerInfo($child);
        });

        return $children;
    }

    /**
     * @return EncapsulatedContentInfo
     * @throws Exception
     */
    public function getEncapsulatedContentInfo()
    {
        /** @var ExplicitlyTaggedObject $EncapsulatedContentInfoSet */
        $sequence = $this->object->findChildrenByType(Sequence::class)[0];

        return new EncapsulatedContentInfo($sequence);
    }
}
