<?php
/**
 * SignedDataContent
 * @see \Adapik\CMS\SignedDataContent
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/CMS-EDITOR
 */

namespace Falseclock\EditCMS;

use Adapik\CMS\AlgorithmIdentifier;
use Adapik\CMS\Certificate;
use Adapik\CMS\RevocationInfoChoices;
use FG\ASN1\Exception\ParserException;
use FG\ASN1\Universal\Sequence;
use FG\ASN1\Universal\Set;

/**
 * Class SignedDataContent
 *
 * @see     \Adapik\CMS\Maps\SignedDataContent
 * @package Falseclock\EditCMS
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
}