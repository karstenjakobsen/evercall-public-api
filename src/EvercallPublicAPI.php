<?php
namespace Evercall;

use GuzzleHttp\Client;

abstract class EvercallPublicAPI {

	protected $client;

	protected $responseBody;

	protected $_payload;

	protected $success = false;

	public function getResponseBody() {
		return $this->responseBody;
	}

	public function success() {
		return $this->success;
	}

	/**
	 * EvercallPublicAPI constructor.
	 * @desc Setup client
	 */
	public function __construct() {

		// Set up
		$this->client = new Client(
			[
				'base_uri'      => 'https://rest-api.evertest.dk/granada/v1/',
				'verify'        => false,
				'http_errors'   => false
			]
		);

	}

	/**
	 * @desc Send request
	 * @return void
	 */
	abstract public function send();

}