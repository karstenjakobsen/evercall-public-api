<?php
namespace Evercall;

class SimpleJsonHttp {

	private $curl;

	private $curlOptions;

	private $requestHeaders;

	private $responseHeaders;

	private $userAgent;

	private $url;

	private $method;

	private $responseBody;

	private $requestBody;

	private $httpStatusCode;

	private $suffix;

	private $httpStatusText;

	private $error = false;

	public function __construct( $userAgent, $url = null ) {
		$this->userAgent 	= $userAgent;
		$this->url 			= $url;
	}

	/**
	 * @param $url
	 * @return $this
	 */
	public function setUrl( $url ) {
		$this->url = $url;
		return $this;
	}

	/**
	 * @param $suffix
	 * @return $this
	 */
	public function setSuffix($suffix)	{
		$this->suffix = $suffix;
		return $this;
	}

	/**
	 * @author Karsten Jakobsen <kj@evercall.dk>
	 * @desc Get full information about request
	 *
	 * @return array
	 */
	public function getResponse() {

		return array(
			'url'             => $this->url.$this->suffix,
			'method'          => $this->method,
			'httpStatusCode'  => $this->httpStatusCode,
			'httpStatusText'  => $this->httpStatusText,
			'responseBody'    => $this->responseBody,
			'responseHeaders' => $this->responseHeaders,
			'requestHeaders'  => $this->requestHeaders,
			'requestBody'     => $this->requestBody
		);

	}

	/**
	 * @return int
	 */
	public function getStatusCode() {
		return (int) $this->httpStatusCode;
	}

	/**
	 * @author Karsten Jakobsen <kj@evercall.dk>
	 * @desc Set a header to send in the request. Additional calls with same header name will overwrite the previously set header
	 *
	 * @param string $headerName Name of the header. Eg. X-username
	 * @param string $headerValue Value of the header. Eg. evercall
	 */
	public function setHeader( $headerName, $headerValue ) {

		if( $this->curl === null )
			$this->_instantiateCurl();

		$this->requestHeaders[$headerName] = trim( $headerValue );
		$this->_setCurlHeaders();

	}

	/**
	 * @author Karsten Jakobsen <kj@evercall.dk>
	 * @desc Remove a header from the header stack.
	 *
	 * @param string $headerName
	 */
	public function removeHeader( $headerName ) {

		unset($this->requestHeaders[$headerName]);
		$this->_setCurlHeaders();

	}

	/**
	 * @author Karsten Jakobsen <kj@evercall.dk>
	 * @desc Instantiate CURL
	 * @return bool
	 */
	private function _instantiateCurl() {

		// Check if instantiated
		if( $this->curl === null ):

			$this->curl = curl_init();

			if( $this->curl === false ):
				die('Error connecting to curl_init()');
			endif;

			$this->curlOptions = array(
				CURLOPT_USERAGENT      => $this->userAgent,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_SSL_VERIFYHOST => false,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_VERBOSE        => false, // Useful for debugging
				CURLOPT_HEADERFUNCTION => array( &$this, '_addResponseHeader' ),
				CURLOPT_CONNECTTIMEOUT => 60
			);

			return true;

		else:

			return false;

		endif;

	}

	/**
	 * @author Karsten Jakobsen <kj@evercall.dk>
	 * @desc Callback function for CURL, adds response headers to the response headers array.
	 *
	 * @param object $curl
	 * @param string $headerLine
	 * @return number
	 */
	private function _addResponseHeader($curl, $headerLine) {

		if (substr($headerLine, 0, 4) == "HTTP"): // Parse the status line. Should regular expressions had been used? :P

			$data				= explode('/', $headerLine);
			$protocol			= $data[0];
			$data				= explode(' ', $headerLine);
			$protocolVersion	= $data[0];
			$statusCode			= $data[1];

			unset($data[0]);
			unset($data[1]);

			$statusText = join(' ', $data);

			$this->httpStatusCode = trim($statusCode);
			$this->httpStatusText = trim($statusText);

		elseif (trim($headerLine) != ''): // Parse normal response header

			$data				= explode(':', $headerLine);
			$headerName			= $data[0]; unset($data[0]); // Remove the value processed...
			$headerValue		= join(':',$data);
			$headerValue		= trim($headerValue);
			$headerName			= trim($headerName);

			$this->responseHeaders[$headerName] = $headerValue;

		endif; // All other lines are ignored...

		return strlen($headerLine);
	}

	private function setCURLError() {

		if( curl_errno( $this->curl ) != 0 ):
			$this->error = curl_error( $this->curl );
		endif;
	}

	/**
	 *
	 * @author Karsten Jakoben <kj@evercall.dk> 15/12/2015
	 * @desc Sends GET request and return self
	 *
	 * @return array
	 *
	 */
	public function get() {
		return $this->doRequest('GET');
	}

	/**
	 * @author Karsten Jakobsen <kj@evercall.dk>
	 * @desc Sends a post request
	 *
	 * @param string $body
	 * @return array
	 */
	public function post( $body = null ) {
		return $this->doRequest('POST', $body);
	}

	/**
	 * @author Karsten Jakobsen <kj@evercall.dk>
	 * @desc Sends a delete request
	 *
	 * @param string $body
	 * @return array
	 */
	public function delete( $body = null ) {
		return $this->doRequest('DELETE', $body);
	}

	/**
	 * @author Karsten Jakobsen <kj@evercall.dk>
	 * @desc Sends a patch request
	 *
	 * @param string $body
	 * @return array
	 */
	public function patch( $body = null ) {
		return $this->doRequest('PATCH', $body);
	}

	private function doRequest( $type, $body ) {

		$this->_instantiateCurl();

		$this->responseHeaders = array();
		$this->method = $type;
		$this->curlOptions[CURLOPT_CUSTOMREQUEST] = $this->method;

		if( $this->method != 'GET' ):
			if( $body !== null && $body != 'null' ):
				$this->requestBody = $body;
				$this->curlOptions[CURLOPT_POSTFIELDS] = $body;
			else:
				if( isset($this->curlOptions[CURLOPT_POSTFIELDS]) ):
					unset($this->curlOptions[CURLOPT_POSTFIELDS]);
				endif;
			endif;
		endif;

		$this->curlOptions[CURLOPT_URL]	  = $this->url.$this->suffix;

		curl_setopt_array( $this->curl, $this->curlOptions );

		$this->responseBody = curl_exec( $this->curl );

		$this->setCURLError();

		return $this->getResponse();

	}


	/**
	 * @author Karsten Jakobsen <kj@evercall.dk>
	 * @desc Updates the CURL requestHeaders array.
	 */
	private function _setCurlHeaders() {

		$headers = array();

		foreach( $this->requestHeaders as $headerName => $headerValue ):

			$headers[] = "{$headerName}: {$headerValue}";

		endforeach;

		$this->curlOptions[CURLOPT_HTTPHEADER] = $headers;
	}
}