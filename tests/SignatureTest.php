<?php
declare(strict_types = 1);

namespace Falseclock\AdvancedCMS\Test;

use Falseclock\AdvancedCMS\OCSPRequest;
use Falseclock\AdvancedCMS\Signature;

class SignatureTest extends MainTest
{
	public function testCreation() {
		$OCSPRequest = OCSPRequest::createFromContent($this->getOCSPRequestWithSignature());

		$optionalSignature = $OCSPRequest->getOptionalSignature();

		$binary = $optionalSignature->getBinary();
		$newSignature = Signature::createFromContent($binary);
		self::assertEquals($binary, $newSignature->getBinary());
	}
}
