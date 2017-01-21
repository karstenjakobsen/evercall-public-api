<?php
namespace Evercall;

class TelemeetingInvitationSMS extends EvercallPublicAPI {

	public $countryCode;

	public $phoneNumber;

	public $sender;

	public $meetingPin;

	public $meetingTime;

	public $executionTime;

	public function addInvitationSMS( $countryCode, $phoneNumber, $sender, $meetingPin, $meetingTime, $executionTime ) {

		$this->countryCode 		= $countryCode;
		$this->phoneNumber 		= $phoneNumber;
		$this->sender 			= $sender;
		$this->meetingPin 		= $meetingPin;
		$this->meetingTime 		= $meetingTime;
		$this->executionTime 	= $executionTime;

		$this->_payload[] = array(
			"country-code" 		=> $countryCode,
			"phone-number" 		=> $phoneNumber,
			"sender" 			=> $sender,
			"meeting-pin" 		=> $meetingPin,
			"meeting-time" 		=> $meetingTime,
			"execution-time" 	=> $executionTime
		);
	}

	public function send() {

		$this->response = $this->client->post($this->baseURL.'/telemeeting/invitation/sms', [
			'json' => [
				'invitations' => $this->_payload
			],
			'http_errors' => false
		]);

	}

	public function success() {
		return $this->response->getStatusCode() == 200 ? true : false;
	}

}