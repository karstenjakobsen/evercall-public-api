<?php
namespace Evercall;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

abstract class EvercallPublicAPI {

	protected $baseURL = 'https://rest-api.evertest.dk/granada/v1';

	/**
	 * @var Client
	 */
	protected $client;

	protected $responseBody;

	/**
	 * @var ResponseInterface
	 */
	protected $response;

	protected $_payload;

	protected $success = false;

	public function getResponseBody() {
		return json_decode($this->response->getBody(),true);
	}

	/**
	 * EvercallPublicAPI constructor.
	 * @desc Setup client
	 * @param $client Client
	 */
	public function __construct( Client $client ) {
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