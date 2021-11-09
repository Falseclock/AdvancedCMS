<?php
/**
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/AdvancedCMS
 */

declare(strict_types=1);

namespace Falseclock\AdvancedCMS\Test;

use Exception;
use PHPUnit\Framework\TestCase;

abstract class MainTest extends TestCase
{
    public function getTimeStampResponseWithAccuracy()
    {
        return file_get_contents(__DIR__ . '/fixtures/TimeStampResponseWithAccuracy.pem');
    }

    public function getOCSPResponse()
    {
        return file_get_contents(__DIR__ . '/fixtures/OCSPResponse.pem');
    }

    public function getTimeStampRequestWithPolicy()
    {
        return file_get_contents(__DIR__ . '/fixtures/TimeStampRequestWithPolicy.pem');
    }

    public function getRevocationValues()
    {
        return file_get_contents(__DIR__ . '/fixtures/RevocationValues.pem');
    }

    public function getFullCMS()
    {
        return file_get_contents(__DIR__ . '/fixtures/full.cms');
    }

    public function getNoDataNoUnsignedCMS()
    {
        return file_get_contents(__DIR__ . '/fixtures/noDataNoUnsigned.cms');
    }

    public function getWithDataNoUnsignedCMS()
    {
        return file_get_contents(__DIR__ . '/fixtures/withDataNoUnsigned.cms');
    }

    public function getIntermediateCertificate()
    {
        return file_get_contents(__DIR__ . '/fixtures/intermediateCertificate.pem');
    }

    public function getSetOfUnsignedCMS()
    {
        return file_get_contents(__DIR__ . '/fixtures/setOfUnsignedCMS.cms');
    }

    public function getTimeStampResponse()
    {
        return file_get_contents(__DIR__ . '/fixtures/TimeStampResponse.pem');
    }

    public function getTimeStampResponseTSA()
    {
        return file_get_contents(__DIR__ . '/fixtures/response.tsr');
    }

    public function getBadTimeStampResponse()
    {
        return file_get_contents(__DIR__ . '/fixtures/BadTimeStampResponse.pem');
    }

    public function getOCSPRequest()
    {
        return file_get_contents(__DIR__ . '/fixtures/OCSPRequest.pem');
    }

    public function getOCSPRequestWithSignature()
    {
        return file_get_contents(__DIR__ . '/fixtures/OCSPRequestWithSignature.pem');
    }

    public function getRevokedCertificate()
    {
        return file_get_contents(__DIR__ . '/fixtures/revokedCertificate.pem');
    }

    public function getDoubleOCSPRequest()
    {
        return file_get_contents(__DIR__ . '/fixtures/DoubleOCSPRequest.pem');
    }

    public function DoubleSignOCSPAndTSPAndData()
    {
        return file_get_contents(__DIR__ . '/fixtures/DoubleSignOCSPAndTSPAndData.cms');
    }

    /**
     * @throws Exception
     */
    protected function curlRequest(string $url, string $binaryContent, string $requestContentType, string $responseContentType)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $binaryContent);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: ' . $requestContentType]);
        /** @noinspection PhpDeprecationInspection */
        curl_setopt($curl, CURLOPT_SAFE_UPLOAD, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($curl);
        $info = curl_getinfo($curl);

        if ($info['http_code'] !== 200) {
            throw new Exception("Unexpected HTTP Status Response: {$info['http_code']}");
        }

        if ($info['content_type'] !== $responseContentType) {
            throw new Exception("Unexpected Content-Type header: {$info['content_type']}");
        }

        // Actually we need only response, and if array is not set - we do not have any errors
        if (!isset($this->processErrors[$url])) {
            return $result ?? null;
        }

        return null;
    }
}
