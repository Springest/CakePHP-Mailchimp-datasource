<?php
App::uses('MailchimpAppController', 'Mailchimp.Controller');

class MailchimpController extends MailchimpAppController {

	public $uses = array('Mailchimp.MailchimpSubscriber');

	public $paginate = array();

	public function beforeFilter() {
		parent::beforeFilter();

		App::import('Vendor', 'Mailchimp.mailchimp/MCAPI.class');
		$this->Mailchimp = new MCAPI(Configure::read('Mailchimp.apiKey'));
	}

/****************************************************************************************
 * USER functions
 ****************************************************************************************/

	/**
	 * Main admin backend for mailchimp
	 *
	 * @return void
	 */
	public function admin_index() {
		$filters = array();
		if ($id = Configure::read('Mailchimp.defaultListId')) {
			$filters['list_id'] = $id;
		}
		$lists = $this->Mailchimp->lists($filters);
		if (empty($lists['data'])) {
			throw new NotFoundException(__('No subscriber list found'));
		}
		$defaultList = array_shift($lists['data']);

		$this->set(compact('defaultList', 'lists'));
	}

/****************************************************************************************
 * protected/interal functions
 ****************************************************************************************/

/****************************************************************************************
 * deprecated/test functions
 ****************************************************************************************/

}