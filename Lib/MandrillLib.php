<?php
App::import('Vendor', 'Mailchimp.Mandrill/Mandrill');
App::uses('CakeLog', 'Log');

class MandrillLib extends Mandrill {

	/**
	 * MandrillLib::__construct()
	 *
	 * @param string $apiKey
	 * @throws Exception If apiKey not found.
	 */
	public function __construct($apiKey = null) {
		$apiKey = Configure::read('Mandrill.apiKey');
		parent::__construct($apiKey);

		//TODO: optionally allow ssl here
		//curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
		//curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);
	}

	/**
	 * Overwrite to allow empty params.
	 *
	 * @param string $url
	 * @param array $params
	 * @return array Result
	 */
	public function call($url, $params = array()) {
		return parent::call($url, $params);
	}

	/**
	 * Overwrite log to write log files in Cake tmp/logs/.
	 *
	 * @param string $msg
	 * @return void
	 */
	public function log($msg) {
		if(!$this->debug) {
			return;
		}
		CakeLog::write('mandrill', $msg);
	}

}