<?php

namespace Falseclock\AdvancedCMS\Test;

use Exception;
use PHPUnit\Framework\TestCase;

abstract class MainTest extends TestCase
{
    public function getOCSPResponse()
    {
        return file_get_contents(__DIR__ . '/../fixtures/OCSPResponse.pem');
    }

    public function getOCSPRequestWithoutSignature()
    {
        return file_get_contents(__DIR__ . '/../fixtures/OCSPRequestWithoutSignature.pem');
    }

    public function getFullCMS()
    {
        return file_get_contents(__DIR__ . '/../fixtures/full.cms');
    }

    public function getNoDataNoUnsignedCMS()
    {
        return file_get_contents(__DIR__ . '/../fixtures/noDataNoUnsigned.cms');
    }

    public function getWithDataNoUnsignedCMS()
    {
        return file_get_contents(__DIR__ . '/../fixtures/withDataNoUnsigned.cms');
    }

    public function getIntermediateCertificate()
    {
        return file_get_contents(__DIR__ . '/../fixtures/intermediateCertificate.pem');
    }

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
