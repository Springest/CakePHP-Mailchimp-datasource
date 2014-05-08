<?php

App::uses('AbstractTransport', 'Network/Email');
App::uses('HttpSocket', 'Network/Http');

/**
 * MandrillTransport
 *
 * This class is used for sending email messages
 * using the Mandrill API http://mandrillapp.com/
 *
 * Use Configure::write('Mandrill') to set defaults
 * - url
 * - apiKey
 *
 * @license MIT
 * @link https://mandrillapp.com/api/docs/
 */
class MandrillTransport extends AbstractTransport {

	const API_URL = 'https://mandrillapp.com/api/1.0/';

	/**
	 * CakeEmail
	 *
	 * @var CakeEmail
	 */
	protected $_Email;

	/**
	 * Variable that holds Mandrill connection
	 *
	 * @var HttpSocket
	 */
	protected $_Socket;

	/**
	 * CakeEmail headers
	 *
	 * @var array
	 */
	protected $_headers;

	/**
	 * Configuration to transport
	 *
	 * @var mixed
	 */
	protected $_config = array();

	/**
	 * Sends out email via Mandrill
	 *
	 * @return array Return the Mandrill
	 */
	public function send(CakeEmail $email) {
		$this->_Email = $email;

		$this->_config = $this->_Email->config() + (array)Configure::read('Mandrill');
		if (empty($this->_config['apiKey'])) {
			throw new InternalErrorException('No API key');
		}
		if (empty($this->_config['uri'])) {
			$this->_config['uri'] = self::API_URL;
		}

		$include = array(
			'from',
			'to',
			'cc',
			'bcc',
			'replyTo',
			'subject');
		$this->_headers = $this->_Email->getHeaders($include);
		$message = $this->_buildMessage();

		$request = array('header' => array(
				'Accept' => 'application/json',
				'Content-Type' => 'application/json',
		));

		$template = $this->_Email->template();
		if ($template['template'] && !empty($this->_config['useTemplate'])) {
			$messageUri = $this->_config['uri'] . "messages/send-template.json";
		} else {
			$messageUri = $this->_config['uri'] . "messages/send.json";
		}

		// Perform the http connection
		$returnMandrill = $this->_post($messageUri, $message, $request);

		// Parse mandrill results
		$result = json_decode($returnMandrill, true);

		$headers = $this->_headersToString($this->_headers);

		return array_merge(array('Mandrill' => $result), array('headers' => $headers, 'message' => $message));
	}

	/**
	 * MandrillTransport::_post()
	 *
	 * @param mixed $messageUri
	 * @param array $message
	 * @param array $request
	 * @return mixed Result of request, either false on failure or the response to the request.
	 */
	protected function _post($messageUri, array $message, array $request = array()) {
		$this->_Socket = new HttpSocket();
		return $this->_Socket->post($messageUri, json_encode($message), $request);
	}

	/**
	 * Build message
	 *
	 * @return array
	 */
	protected function _buildMessage() {
		$json = array();
		$json['key'] = $this->_config['apiKey'];
		$template = $this->_Email->template();
		if ($template['template'] && !empty($this->_config['useTemplate'])) {
			$json['template_name'] = $template['layout'] . '-' . $template['template'];
			$json['template_content'] = array();
		}

		$message = array();

		$mergeVars = array();
		$viewVars = $this->_Email->viewVars();
		foreach ($viewVars as $key => $content) {
			$mergeVars[] = array('name' => $key, 'content' => $content);
		}

		$message['merge_vars'] = array();
		$message['merge_vars'][] = array('rcpt' => $this->_headers['To'], 'vars' => $mergeVars);

		$message['from_email'] = substr($this->_headers['From'], strpos($this->_headers['From'], '<') + 1, -1);
		$message['from_name'] = trim(substr($this->_headers['From'], 0, strpos($this->_headers['From'], '<')));
		$message['from_name'] = trim($message['from_name'], '"');

		$email = substr($this->_headers['To'], strpos($this->_headers['To'], '<') + 1, -1);
		$name = trim(substr($this->_headers['To'], 0, strpos($this->_headers['To'], '<')));
		$name = trim($name, '"');
		$message['to'] = array(array('email' => $email, 'name' => $name));

		$message['subject'] = mb_decode_mimeheader($this->_headers['Subject']);

		if ($this->_Email->emailFormat() === 'html' || $this->_Email->emailFormat() === 'both') {
			$Reflection = new ReflectionProperty(get_class($this->_Email), '_htmlMessage');
      $Reflection->setAccessible(true);
			$message['html'] = $Reflection->getValue($this->_Email); // $this->_Email->_htmlMessage;
		}

		if ($this->_Email->emailFormat() === 'text' || $this->_Email->emailFormat() === 'both') {
			$Reflection = new ReflectionProperty(get_class($this->_Email), '_textMessage');
			$Reflection->setAccessible(true);
			$message['text'] = $Reflection->getValue($this->_Email); //$this->_Email->_textMessage;
		}

		$json['message'] = $message;

		return $json;
	}

}
