<?php
namespace Evercall;

class TelemeetingInvitationSMSTest extends \PHPUnit_Framework_TestCase  {

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

		$this->setClientResponse(200);

		$invitation = new TelemeetingInvitationSMS($this->client);
		$invitation->send();

		$this->assertEquals(true, $invitation->success());
	}

	public function test_should_returnError_when_returnCodeIs400() {

		$this->setClientResponse(400);

		$invitation = new TelemeetingInvitationSMS($this->client);
		$invitation->send();

		$this->assertEquals(false, $invitation->success());
	}

	public function test_should_returnArrayBody_when_responseContainsJSON() {

		$this->setClientResponse(200, '{"body":"test"}');

		$invitation = new TelemeetingInvitationSMS($this->client);
		$invitation->send();

		$this->assertEquals(true, is_array($invitation->getResponseBody()));
	}

	public function test_should_haveValidDateInBody_when_executionTimeIsNegativeInteger() {

		$this->setClientResponse(200);

		$invitation = new TelemeetingInvitationSMS($this->client);
		$invitation->addInvitationSMS(45,31712929,'kalle','12345678','2016-01-01', -600);
		$payload = $invitation->getPayload();

		$this->assertEquals(true, is_int(strtotime($payload[0]["executionTime"])));
	}

	public function test_should_haveValidDateInBody_when_executionTimeIsNegativeStringInteger() {

		$this->setClientResponse(200);

		$invitation = new TelemeetingInvitationSMS($this->client);
		$invitation->addInvitationSMS(45,31712929,'kalle','12345678','2016-01-01', "-600");
		$payload = $invitation->getPayload();

		$this->assertEquals(true, is_int(strtotime($payload[0]["executionTime"])));
	}

	public function test_should_haveValidDateInBody_when_executionTimeIsDate() {

		$this->setClientResponse(200);

		$invitation = new TelemeetingInvitationSMS($this->client);
		$invitation->addInvitationSMS(45,31712929,'kalle','12345678','2016-01-01', '2016-02-01');
		$payload = $invitation->getPayload();

		$this->assertEquals(true, is_int(strtotime($payload[0]["executionTime"])));
	}

	public function test_should_haveValidDateInBody_when_meetingTimeIsDate() {

		$this->setClientResponse(200);

		$invitation = new TelemeetingInvitationSMS($this->client);
		$invitation->addInvitationSMS(45,31712929,'kalle','12345678','2016-01-01', '2016-02-01');
		$payload = $invitation->getPayload();

		$this->assertEquals(true, is_int(strtotime($payload[0]["meetingTime"])));
	}

}