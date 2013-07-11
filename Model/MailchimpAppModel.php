<?php
App::uses('AppModel', 'Model');

class MailchimpAppModel extends AppModel {

	public $useDbConfig = 'mailchimp';

	public $useTable = false;

	public $Mailchimp;

	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		$config = array_merge((array)Configure::read('Mailchimp'), array());
		$this->settings = $config;

		App::import('Vendor', 'Mailchimp.mailchimp/MCAPI.class');
		$this->Mailchimp = new MCAPI(Configure::read('Mailchimp.apiKey'));
	}

	/**
	 * MailchimpAppModel::getError()
	 *
	 * @return string Error message
	 */
	public function getError() {
		if (!$this->Mailchimp->errorCode) {
			return 'Error ' . $this->Mailchimp->errorCode . ': ' . $this->Mailchimp->errorMessage;
		}
		return '';
	}

}