<?php
namespace Evercall;

class PingTest extends \PHPUnit_Framework_TestCase  {

	protected $client;

	public function setUp()
	{
		parent::setUp();
	}

	private function setClientResponse($statusCode, $body = null) {

		// Create a mock and queue two responses.
		$this->client = $this->createMock('Evercall\SimpleJsonHttp');

		// Configure the stub.
		$this->client->method('getStatusCode')
			->willReturn($statusCode);

		// Configure the stub.
		$this->client->method('getResponse')
			->willReturn(array('responseBody' => $body));

	}

	public function test_should_returnSuccess_when_returnCodeIs200() {

		$this->setClientResponse(200, '{"body":"test"}');

		$invitation = new Ping($this->client);
		$invitation->send();

		$this->assertEquals(true, $invitation->success());
	}
}