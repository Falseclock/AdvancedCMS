<?php
/**
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/AdvancedCMS
 */

declare(strict_types=1);

namespace Falseclock\AdvancedCMS\Test;

use Adapik\CMS\MessageImprint;
use Falseclock\AdvancedCMS\Template;
use Falseclock\AdvancedCMS\TimeStampRequest;
use Falseclock\AdvancedCMS\TimeStampResponse;
use FG\ASN1\Universal\Boolean;
use FG\ASN1\Universal\Integer;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\OctetString;

class TimeStampRequestTest extends MainTest
{
    public function testPolicy()
    {
        $timeStampRequest = TimeStampRequest::createFromContent($this->getTimeStampRequestWithPolicy());

        $policy = $timeStampRequest->getReqPolicy();
        self::assertInstanceOf(ObjectIdentifier::class, $policy);
        self::assertEquals("1.2.3.4.5.6.7.8.9", (string)$policy);
        self::assertNull($timeStampRequest->getCertReq());
    }

    function testCreate()
    {
        $binary = "123456";
        $timeStampRequest = Template::TimeStampRequest(OctetString::createFromString($binary));

        $timeStampRequest = TimeStampRequest::createFromContent($timeStampRequest->getBinary());

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
