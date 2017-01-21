<?php
namespace Evercall;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class TelemeetingInvitationSMSTest extends \PHPUnit_Framework_TestCase  {

	protected $client;

	public function setUp()
	{
		parent::setUp();
	}

	private function setClient($statusCode, array $headers, $body = null) {

		// Create a mock and queue two responses.
		$mock = new MockHandler([
			new Response($statusCode, $headers, $body)
		]);

		$handler = HandlerStack::create($mock);
		$this->client = new Client(['handler' => $handler]);

	}

	public function test_should_returnSuccess_when_returnCodeIs200() {

		$this->setClient(200, []);

		$invitation = new TelemeetingInvitationSMS($this->client);
		$invitation->send();

		$this->assertEquals(true, $invitation->success());
	}

	public function test_should_returnError_when_returnCodeIs400() {

		$this->setClient(400, []);

		$invitation = new TelemeetingInvitationSMS($this->client);
		$invitation->send();

		$this->assertEquals(false, $invitation->success());
	}

	public function test_should_returnArrayBody_when_responseContainsJSON() {

		$this->setClient(200, [], '{"body":"test"}');

		$invitation = new TelemeetingInvitationSMS($this->client);
		$invitation->send();

		$this->assertEquals(true, is_array($invitation->getResponseBody()));
	}

}