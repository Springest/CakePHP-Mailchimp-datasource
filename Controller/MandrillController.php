<?php
App::uses('MailchimpAppController', 'Mailchimp.Controller');
App::uses('MandrillLib', 'Mailchimp.Lib');

class MandrillController extends MailchimpAppController {

	public $uses = array();

	public function beforeFilter() {
		parent::beforeFilter();
	}

	/**
	 * Main admin backend for mandrill
	 *
	 * List verification details for our primary domain.
	 *
	 * @return void
	 * @throws NotFoundException
	 */
	public function admin_index() {
		$Mandrill = new MandrillLib();

		//$senders = $Mandrill->call('senders/list');
		//$domains = $Mandrill->call('senders/domains');

		$email = Configure::read('Contact.email');
		$domain = substr($email, strpos($email, '@') + 1);
		$result = $Mandrill->call('senders/check-domain', array('domain' => $domain));
		$this->set(compact('result', 'domain'));
	}

}