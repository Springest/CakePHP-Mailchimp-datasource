<?php
App::uses('AppModel', 'Model');

class MailchimpAppModel extends AppModel {

	public $useDbConfig = 'mailchimp';

	public $useTable = false;

	/**
	 * Response when not using exceptions
	 *
	 * @var array
	 */
	public $response = array();

	public $Mailchimp;

	protected $_defaults = array(
		'exceptions' => false,
		'apiKey' => '',
		'defaultListId' => '',
		'defaultCampaignId' => '',
	);

	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		$this->settings = array_merge($this->_defaults, (array)Configure::read('Mailchimp'));

		App::import('Vendor', 'Mailchimp.MailChimp/MailChimp');
		$this->Mailchimp = new \Drewm\MailChimp(Configure::read('Mailchimp.apiKey'));
	}

	/**
	 * MailchimpAppModel::call()
	 *
	 * @return mixed
	 */
	public function call($method, array $options = array()) {
		$args = array();
		foreach ($options as $key => $value) {
			$args[Inflector::underscore($key)] = $value;
		}
		$this->response = $this->Mailchimp->call($method, $args);
		if (!isset($this->response['status'])) {
			return $this->response;
		}
		if ($this->settings['exceptions']) {
			throw new MailchimpException($this->getError());
		}
		return false;
	}

	/**
	 * MailchimpAppModel::getError()
	 *
	 * @return string Error message
	 */
	public function getError($full = false) {
		if (isset($this->response['status'])) {
			return 'Error ' . $this->response['code'] . ': ' . $this->response['error'];
		}
		return '';
	}

}

class MailchimpException extends CakeException {
}