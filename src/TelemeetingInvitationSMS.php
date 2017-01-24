<?php
namespace Evercall;

class TelemeetingInvitationSMS extends EvercallPublicAPI {

	public function __construct(SimpleJsonHttp $client)
	{
		parent::__construct($client);

		$this->client->setSuffix('telemeeting/invitation/sms');

	}

	/**
	 * @param $meetingTime
	 * @param $executionTime
	 * @return false|string
	 */
	private function formatExecutionTime($meetingTime, $executionTime) {

		if( $meetingTime == false || $executionTime < 0  ) return false;

		// If $executionTime is a negative integer then calculate
		if( is_numeric($executionTime) && is_int( (int) $executionTime) ):
			$executionTime = strtotime($meetingTime) - (int) $executionTime;
			return date('c', $executionTime);
		else:
			if( strtotime($executionTime) == 0 ) return false;
			return date('c', strtotime($executionTime));
		endif;
	}

	/**
	 * @param $meetingTime
	 * @return false|string
	 */
	private function formatMeetingTime($meetingTime) {
		return strtotime($meetingTime) == 0 ? false : date('c', strtotime($meetingTime));
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
			$this->addSingle( $countryCode, $phoneNumber, $sender, $meetingPin, $meetingTime, $executionTime );
		endforeach;
	}

	/**
	 * @param $countryCode
	 * @param $phoneNumber
	 * @param $sender
	 * @param $meetingPin
	 * @param $meetingTime
	 * @param $executionTime
	 * @return void
	 */
	private function addSingle( $countryCode, $phoneNumber, $sender, $meetingPin, $meetingTime, $executionTime ) {

		$this->_payload[] = array(
			"countryCode" 		=> $countryCode,
			"phoneNumber" 		=> $phoneNumber,
			"sender" 			=> $sender,
			"meetingPin" 		=> $meetingPin,
			"meetingTime" 		=> $meetingTime,
			"executionTime" 	=> $executionTime,
		);

		$this->body = array( 'invitations' => $this->_payload );
	}

	/**
	 * @param $countryCode
	 * @param $phoneNumber
	 * @param $sender
	 * @param $meetingPin
	 * @param $meetingTime
	 * @param $executionTime
	 * @return void
	 */
	public function addInvitationSMS( $countryCode, $phoneNumber, $sender, $meetingPin, $meetingTime, $executionTime ) {

		$meetingTime 	= $this->formatMeetingTime($meetingTime);
		$executionTime 	= $this->formatExecutionTime($meetingTime, $executionTime);

		// Check times are valid
		if( $meetingTime !== false && $executionTime !== false ):
			// Check if array. Create multiple
			if( is_array($countryCode) && is_array($countryCode) && count($countryCode) == count($phoneNumber) ):
				$this->addMultiple( $countryCode, $phoneNumber, $sender, $meetingPin, $meetingTime, $executionTime );
			else:
				$this->addSingle( $countryCode, $phoneNumber, $sender, $meetingPin, $meetingTime, $executionTime );
			endif;
		endif;

	}

	public function send() {
		$this->client->post(json_encode($this->body));
	}

	public function success() {
		return $this->client->getStatusCode() == 200 ? true : false;
	}

}