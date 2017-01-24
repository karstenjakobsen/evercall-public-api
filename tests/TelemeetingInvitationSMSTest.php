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

	public function test_should_returnResponse_when_runningInSandboxMode() {

		$invitation = new TelemeetingInvitationSMS(new SimpleJsonHttp('Kalle', null, true));
		$invitation->addInvitationSMS(array('45','46'), array('3171','2929'),1,2,'2016-01-01', 9200);
		$invitation->send();

		$this->assertEquals(true, is_array($invitation->getResponse()));
	}

	public function test_should_returnMethod_when_runningInSandboxMode() {

		$invitation = new TelemeetingInvitationSMS(new SimpleJsonHttp('Kalle', null, true));
		$invitation->addInvitationSMS(array('45','46'), array('3171','2929'),1,2,'2016-01-01', 9200);
		$invitation->send();

		$this->assertArrayHasKey('method',$invitation->getResponse());
	}

	public function test_should_haveMultiplePayloads_when_addingMultiplePhones() {

		$this->setClientResponse(200, '{"body":"test"}');

		$invitation = new TelemeetingInvitationSMS($this->client);
		$invitation->addInvitationSMS(array('45','46'), array('3171','2929'),1,2,'2016-01-01', 9200);
		$invitation->addInvitationSMS(array('45','46'), array('5151','4141'),1,2,'2016-01-01', 9200);

		$payload = $invitation->getBody();
		$this->assertEquals(4, count($payload['invitations']));
	}

	public function test_should_haveSinglePayload_when_addingOnePhone() {

		$this->setClientResponse(200, '{"body":"test"}');

		$invitation = new TelemeetingInvitationSMS($this->client);
		$invitation->addInvitationSMS(45,1234,1,2,'2016-01-01', 9200);

		$payload = $invitation->getBody();
		$this->assertEquals(1, count($payload['invitations']));
	}

	public function test_should_haveValidDateInBody_when_executionTimeIsAnInteger() {

		$this->setClientResponse(200);

		$invitation = new TelemeetingInvitationSMS($this->client);
		$invitation->addInvitationSMS(45,31712929,'kalle','12345678','2016-01-01', 9200);
		$payload = $invitation->getBody();

		$this->assertEquals(true, is_int(strtotime($payload['invitations'][0]["executionTime"])));
	}

	public function test_should_haveValidDateInBody_when_executionTimeIsAnStringInteger() {

		$this->setClientResponse(200);

		$invitation = new TelemeetingInvitationSMS($this->client);
		$invitation->addInvitationSMS(45,31712929,'kalle','12345678','2016-01-01', 3600);
		$payload = $invitation->getBody();

		$this->assertEquals(1, count($payload['invitations']));
	}

	public function test_should_haveValidDateInBody_when_executionTimeIsDate() {

		$this->setClientResponse(200);

		$invitation = new TelemeetingInvitationSMS($this->client);
		$invitation->addInvitationSMS(45,31712929,'kalle','12345678','2016-01-01', '2016-02-01');
		$payload = $invitation->getBody();

		$this->assertEquals(true, is_int(strtotime($payload['invitations'][0]["executionTime"])));
	}

}