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

		$this->client->setSuffix('/telemeeting/invitation/sms');

		$body = array( 'invitations' => $this->_payload );
		$this->client->post(json_encode($body));

	}

	public function success() {
		return $this->client->getStatusCode() == 200 ? true : false;
	}

}