<?php
namespace Evercall;

class TelemeetingInvitationSMS extends EvercallPublicAPI {

	public function addInvitationSMS( $countryCode, $phoneNumber, $sender, $meetingPin, $meetingTime, $executionTime ) {

		$this->_payload[] = array(
			"countryCode" 		=> $countryCode,
			"phoneNumber" 		=> $phoneNumber,
			"sender" 			=> $sender,
			"meetingPin" 		=> $meetingPin,
			"meetingTime" 		=> $meetingTime,
			"executionTime" 	=> $executionTime
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