<?php
namespace Evercall;

class TelemeetingInvitationSMS extends EvercallPublicAPI {

	/**
	 * @param $executionTime
	 * @return false|string
	 */
	private function formatExecutionTime($executionTime) {

		// If $executionTime is a negative integer then calculate
		if( is_int($executionTime) && $executionTime < 0 ):
			return date('c', strtotime(("{$executionTime} seconds")));
		else:
			return date('c', strtotime($executionTime));
		endif;
	}

	/**
	 * @param $meetingTime
	 * @return false|string
	 */
	private function formatMeetingTime($meetingTime) {
		return date('c', strtotime($meetingTime));
	}

	public function addInvitationSMS( $countryCode, $phoneNumber, $sender, $meetingPin, $meetingTime, $executionTime ) {

		$this->payload[] = array(
			"countryCode" 		=> $countryCode,
			"phoneNumber" 		=> $phoneNumber,
			"sender" 			=> $sender,
			"meetingPin" 		=> $meetingPin,
			"meetingTime" 		=> $this->formatMeetingTime($meetingTime),
			"executionTime" 	=> $this->formatExecutionTime($executionTime)
		);
	}

	public function send() {

		$this->client->setSuffix('/telemeeting/invitation/sms');

		$body = array( 'invitations' => $this->payload );
		$this->client->post(json_encode($body));

	}

	public function success() {
		return $this->client->getStatusCode() == 200 ? true : false;
	}

}