<?php

namespace Falseclock\AdvancedCMS\Test;

use Adapik\CMS\MessageImprint;
use Falseclock\AdvancedCMS\TimeStampRequest;
use Falseclock\AdvancedCMS\TimeStampResponse;
use FG\ASN1\Universal\Boolean;
use FG\ASN1\Universal\Integer;
use FG\ASN1\Universal\OctetString;

class TimeStampRequestTest extends MainTest
{
    function testCreate()
    {
        $binary = "123456";
        $timeStampRequest = TimeStampRequest::createSimple(OctetString::createFromString($binary));

        $messageImprint = $timeStampRequest->getMessageImprint();
        self::assertInstanceOf(MessageImprint::class, $messageImprint);
        $messageImprint->getHashAlgorithm();
        $messageImprint->getHashedMessage();

        $certReq = $timeStampRequest->getCertReq();
        self::assertNotNull($certReq);
        self::assertInstanceOf(Boolean::class, $certReq);

        $nonce = $timeStampRequest->getNonce();
        self::assertNotNull($nonce);
        self::assertInstanceOf(Integer::class, $nonce);

        $reqPolicy = $timeStampRequest->getReqPolicy();
        self::assertNull($reqPolicy);

        $TimeStampResponse = null;
        $result = $this->curlRequest('http://tsp.pki.gov.kz', $timeStampRequest->getBinary(), TimeStampRequest::CONTENT_TYPE, TimeStampResponse::CONTENT_TYPE);

        if (!is_null($result)) {
            $TimeStampResponse = TimeStampResponse::createFromContent($result);
        }

        self::assertNotNull($TimeStampResponse);


    }
}