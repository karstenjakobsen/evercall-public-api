<?php
namespace Evercall;

class Ping extends EvercallPublicAPI {

	public function __construct(SimpleJsonHttp $client)	{
		parent::__construct($client);
		$this->client->setSuffix('ping');
	}

	public function setPayload( array $payload ) {
		$this->_payload = $payload;
	}

	public function send() {
		$this->client->post(json_encode($this->_payload));
	}

	public function success() {
		return $this->client->getStatusCode() == 200 ? true : false;
	}

}