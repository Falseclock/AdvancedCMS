<?php
/**
 * SignedData
 * @see \Adapik\CMS\SignedData
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/CMS-EDITOR
 */

namespace Falseclock\EditCMS;

use Exception;
use FG\ASN1\ExplicitlyTaggedObject;

/**
 * Class SignedData
 *
 * @see     \Adapik\CMS\Maps\SignedData
 * @package Falseclock\EditCMS
 */
class SignedData extends \Adapik\CMS\SignedData
{
    /**
     * @param SignedData $signedData
     *
     * @return $this
     * @throws Exception
     */
    public function mergeCMS(SignedData $signedData)
    {
        $initialContent = $this->getSignedDataContent();
        $newContent = $signedData->getSignedDataContent();

        /**
         * @see \Adapik\CMS\Maps\SignedDataContent
         * Append
         * 1. digestAlgorithms
         * 2. certificates
         * 3. crl
         * 4. signerInfos
         */

        foreach ($newContent->getDigestAlgorithmIdentifiers() as $digestAlgorithmIdentifier) {
            $initialContent->appendDigestAlgorithmIdentifier($digestAlgorithmIdentifier);
        }

        foreach ($newContent->getCertificateSet() as $certificate) {
            $initialContent->appendCertificate($certificate);
        }

        $revocationInfoChoices = $newContent->getRevocationInfoChoices();
        if ($revocationInfoChoices) {
            foreach ($revocationInfoChoices as $revocationInfoChoice) {
                $initialContent->appendRevocationInfoChoices($revocationInfoChoice);
            }
        }

        foreach ($newContent->getSignerInfoSet() as $signerInfo) {
            $initialContent->appendSignerInfo($signerInfo);
        }

        return $this;
    }

    /**
     * Message content
     * @return SignedDataContent
     * @throws Exception
     */
    public function getSignedDataContent()
    {
        $SignedDataContent = $this->object->findChildrenByType(ExplicitlyTaggedObject::class)[0];

        return new SignedDataContent($SignedDataContent->getChildren()[0]);
    }
}