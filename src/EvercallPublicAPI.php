<?php
namespace Evercall;

abstract class EvercallPublicAPI {

	const ENV_PROD 		= 'https://rest-api.evercall.dk';

	const ENV_DEV 		= 'https://rest-api.evertest.dk';

	const VERSION_1 	= 'v1';

	const API_NAMESPACE = 'granada';

	/**
	 * @var SimpleJsonHttp
	 */
	protected $client;

	protected $responseBody;

	protected $response;

	protected $_payload;

	protected $body;

	protected $success = false;

	public function setEnv( $env, $version = self::VERSION_1 ) {

		switch ($env):
			case self::ENV_PROD:
			case self::ENV_DEV:
				break;
			default:
				throw new \Exception('Unknown environment');
		endswitch;

		switch ($version):
			case self::VERSION_1:
				break;
			default:
				throw new \Exception('Unknown version');
		endswitch;

		$this->client->setUrl($env.'/'.self::API_NAMESPACE.'/'.$version.'/');

	}

	/**
	 * @param bool $pretty
	 * @return string
	 */
	public function getResponseBody( $pretty = false) {
		$response = $this->client->getResponse();
		return ($pretty) ? json_encode(json_decode($response['responseBody'],true), JSON_PRETTY_PRINT ) : $response['responseBody'];
	}

	public function getResponse() {
		return $this->client->getResponse();
	}

	public function getBody() {
		return $this->body;
	}

	/**
	 * EvercallPublicAPI constructor.
	 * @desc Setup client
	 * @param $client SimpleJsonHttp
	 */
	public function __construct( SimpleJsonHttp $client ) {
		$this->client = $client;
		$this->setEnv(self::ENV_PROD, self::VERSION_1);
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