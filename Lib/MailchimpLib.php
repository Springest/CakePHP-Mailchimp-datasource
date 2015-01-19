<?php
App::uses('CakeLog', 'Log');
App::uses('HttpSocket', 'Network/Http');

/**
 * Super-simple, minimum abstraction MailChimp API v2 wrapper.
 *
 * Uses HttpSocket and HTTP streams.
 *
 * Features:
 * - Set API key via Configure globally
 * - Uses ssl by default
 * - Allows to see the error via Mailchimp->error()
 * - Logs in log file if debug is enabled.
 *
 * @author Mark Scherer
 */
class MailchimpLib {

	public $verify_ssl = true;

	public $debug = false;

	protected $_api_key = '';

	protected $_api_endpoint = 'https://<dc>.api.mailchimp.com/2.0';

	protected $_error = '';

	/**
	 * Create a new instance
	 * @param string $api_key Your MailChimp API key
	 */
	public function __construct($api_key = null) {
		if ($api_key === null) {
			$api_key = Configure::read('Mailchimp.apiKey');
		}
		$this->_api_key = $api_key;
		list(, $datacentre) = explode('-', $this->_api_key);
		$this->_api_endpoint = str_replace('<dc>', $datacentre, $this->_api_endpoint);
	}

	/**
	 * Calls an API method.
	 *
	 * @param string $method The API method to call, e.g. 'lists/list'
	 * @param array  $args An array of arguments to pass to the method. Will be JSON-encoded for you.
	 * @return array Associative array of JSON decoded API response.
	 */
	public function call($method, $args = array(), $timeout = 10) {
		$args['apikey'] = $this->_api_key;

		$url = $this->_api_endpoint . '/' . $method . '.json';

		$result = $this->_get($url, $args);

		$result = $result ? json_decode($result, true) : array();

		$this->log($result ? print_r($result, true) : ('ERROR' . ($this->_error ? ': ' . $this->_error : '')));
		return $result;
	}

	/**
	 * Does the actual API request. Mock this part for testing.
	 *
	 * @param string $url
	 * @param array $args
	 * @return string
	 */
	protected function _get($url, $args) {
		$Socket = new HttpSocket(array(
			'ssl_cafile' => CAKE . 'Config' . DS . 'cacert.pem',
		));
		$result = $Socket->post($url, $args);
		return $result->body;
		/*
		If you need to use curl...
		//$json_data = json_encode($args);
		if (function_exists('curl_init') && function_exists('curl_setopt')) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
			curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-MCAPI/2.0');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->verify_ssl);
			//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
			if (!is_file(CAKE . 'Config' . DS . 'cacert.pem')) {
				throw new InternalErrorException('Cannot find pem file');
			}
			curl_setopt($ch, CURLOPT_SSLCERT, CAKE . 'Config' . DS . 'cacert.pem');
			curl_setopt($ch, CURLOPT_SSLKEY, CAKE . 'Config' . DS . 'cacert.pem');

			curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
			$result = curl_exec($ch);
			if (!$result) {
				$this->_error = curl_error($ch);
			}
			curl_close($ch);
			//debug($this->_error);
		} else {
			$options = array(
				'http' => array(
					'protocol_version' => 1.1,
					'user_agent' => 'PHP-MCAPI/2.0',
					'method' => 'POST',
					'header' => "Content-type: application/json\r\n" . "Connection: close\r\n" . "Content-length: " . strlen($json_data) . "\r\n",
					'content' => $json_data,
				)
			);
			$result = file_get_contents($url, null, stream_context_create($options));
		}
		*/
	}

	/**
	 * MailChimp::error()
	 *
	 * @return string
	 */
	public function error() {
		return $this->_error;
	}

	/**
	 * Overwrite log to write log files in Cake tmp/logs/.
	 *
	 * @param string $msg
	 * @return void
	 */
	public function log($msg) {
		if (!$this->debug) {
			return;
		}
		CakeLog::write('mailchimp', $msg);
	}

}
