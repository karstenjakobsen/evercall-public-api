<?php
namespace Evercall;

class TelemeetingInvitationSMS extends EvercallPublicAPI {

	public function __construct(SimpleJsonHttp $client)
	{
		parent::__construct($client);

		$this->client->setUrl('https://rest-api.evertest.dk/granada/v1/telemeeting/invitation/sms');
	}

	/**
	 * @param $executionTime
	 * @return false|string
	 */
	private function formatExecutionTime($executionTime) {

		// If $executionTime is a negative integer then calculate
		if( is_numeric($executionTime) && $executionTime < 0 ):
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

	/**
	 * @param array $countryCode
	 * @param array $phoneNumber
	 * @param $sender
	 * @param $meetingPin
	 * @param $meetingTime
	 * @param $executionTime
	 */
	private function addMultiple( array $countryCode, array $phoneNumber, $sender, $meetingPin, $meetingTime, $executionTime ) {

		foreach ( $countryCode as $key => $value ):

			$this->_payload[] = array(
				"countryCode" 		=> $value,
				"phoneNumber" 		=> $phoneNumber[$key],
				"sender" 			=> $sender,
				"meetingPin" 		=> $meetingPin,
				"meetingTime" 		=> $this->formatMeetingTime($meetingTime),
				"executionTime" 	=> $this->formatExecutionTime($executionTime)
			);

		endforeach;

		$this->body = array( 'invitations' => $this->_payload );
	}

	/**
	 * @param $countryCode
	 * @param $phoneNumber
	 * @param $sender
	 * @param $meetingPin
	 * @param $meetingTime
	 * @param $executionTime
	 */
	public function addInvitationSMS( $countryCode, $phoneNumber, $sender, $meetingPin, $meetingTime, $executionTime ) {

		// Check if array. Create multiple
		if(is_array($countryCode) && is_array($countryCode) && count($countryCode) == count($phoneNumber) ):
			$this->addMultiple( $countryCode, $phoneNumber, $sender, $meetingPin, $meetingTime, $executionTime );
		else:
			$this->_payload[] = array(
				"countryCode" 		=> $countryCode,
				"phoneNumber" 		=> $phoneNumber,
				"sender" 			=> $sender,
				"meetingPin" 		=> $meetingPin,
				"meetingTime" 		=> $this->formatMeetingTime($meetingTime),
				"executionTime" 	=> $this->formatExecutionTime($executionTime)
			);
		endif;

		$this->body = array( 'invitations' => $this->_payload );
	}

	public function send() {
		$this->client->post(json_encode($this->body));
	}

	public function success() {
		return $this->client->getStatusCode() == 200 ? true : false;
	}

}