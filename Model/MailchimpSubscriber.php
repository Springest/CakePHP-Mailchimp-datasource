<?php
App::uses('MailchimpAppModel', 'Mailchimp.Model');

class MailchimpSubscriber extends MailchimpAppModel {

	public $useDbConfig = 'mailchimp';

	public $useTable = false;

	public $validate = array(
		'email' => array(
			'email' => array(
				'rule' => array('email'),
				'message' => 'Please enter a valid e-mail address'
			)
		)
	);

	/**
	 * Use $_schema to set any mailchimp fields that you want to use
	 * @var <type>
	 */
	protected $_schema = array(
		'id' => array(
			'type' => 'int',
			'null' => true,
			'key' => 'primary',
			'length' => 11,
		),
		'email' => array(
			'type' => 'string',
			'null' => false,
			'length' => 256
		),
		'fname' => array(
			'type' => 'string',
			'null' => true,
			'key' => 'primary',
			'length' => 128
		),
		'lname' => array(
			'type' => 'string',
			'null' => true,
			'length' => 128
		),
		'gender' => array(
			'type' => 'string',
			'null' => true,
			'length' => 32
		),
	);

	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		$config = array_merge((array)Configure::read('Mailchimp'), array());
		$this->settings = $config;

		App::import('Vendor', 'Mailchimp.mailchimp/MCAPI.class');
		$this->Mailchimp = new MCAPI(Configure::read('Mailchimp.apiKey'));
	}

	/**
	 * @return boolean
	 */
	public function subscribe($queryData = array()) {
		$response = $this->Mailchimp->listSubscribe($this->settings['defaultListId'], $queryData['email']);
		return $response;
	}

	/**
	 * @return boolean
	 */
	public function unsubscribe($queryData = array()) {
		$response = $this->Mailchimp->listUnsubscribe($this->settings['defaultListId'], $queryData['email']);
		return $response;
	}

	/**
	 * Override del method from model.php class, because it would block deleting when useTable = false and no records exists
	 * @param <type> $id
	 * @param <type> $cascade
	 * @return <type>
	 */
	/*
	public function delete($id = null, $cascade = true) {
		if (!empty($id)) {
			$this->id = $id;
		}
		$id = $this->id;

		if ($this->beforeDelete($cascade)) {
			$db = ConnectionManager::getDataSource($this->useDbConfig);
			if (!$this->Behaviors->trigger($this, 'beforeDelete', array($cascade), array('break' => true, 'breakOn' => false))) {
				return false;
			}
			$this->_deleteDependent($id, $cascade);
			$this->_deleteLinks($id);
			$this->id = $id;

			if (!empty($this->belongsTo)) {
				$keys = $this->find('first', array('fields' => $this->__collectForeignKeys()));
			}

			if ($db->delete($this)) {
				if (!empty($this->belongsTo)) {
					$this->updateCounterCache($keys[$this->alias]);
				}
				$this->Behaviors->trigger($this, 'afterDelete');
				$this->afterDelete();
				$this->_clearCache();
				$this->id = false;
				$this->__exists = null;
				return true;
			}
		}
		return false;
	}
	*/

}