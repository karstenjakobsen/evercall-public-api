<?php
namespace Evercall;

class Telemeeting extends EvercallPublicAPI {

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

		return $this;

	}

	public function send() {

		$response = $this->client->post('telemeeting/invitation/sms', [
			'json' => [
				'invitations' => $this->_payload
			]
		]);

		// Set response
		$this->responseBody = json_decode($response->getBody(), true);

		if($response->getStatusCode() == 200)
			$this->success = true;

	}

}