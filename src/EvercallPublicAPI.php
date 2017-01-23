<?php
namespace Evercall;

abstract class EvercallPublicAPI {

	/**
	 * @var SimpleJsonHttp
	 */
	protected $client;

	protected $responseBody;

	protected $response;

	protected $_payload;

	protected $success = false;

	public function getResponseBody() {
		$response = $this->client->getResponse();
		return json_decode($response['responseBody'],true);
	}

	/**
	 * EvercallPublicAPI constructor.
	 * @desc Setup client
	 * @param $client SimpleJsonHttp
	 */
	public function __construct( SimpleJsonHttp $client ) {
		$this->client = $client;
	}

	/**
	 * @desc Send request
	 * @return void
	 */
	abstract public function send();

	/**
	 * @return bool
	 */
	abstract public function success();

}