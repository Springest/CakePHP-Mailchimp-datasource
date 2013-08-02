<?php
App::uses('AppModel', 'Model');

class MailchimpAppModel extends AppModel {

	public $useDbConfig = 'mailchimp';

	public $useTable = false;

	public $Mailchimp;

	protected $_defaults = array(
		'apiKey' => '',
		'defaultListId' => '',
		'defaultCampaignId' => '',
	);

	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		$this->settings = array_merge($this->defaults, (array)Configure::read('Mailchimp'));

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