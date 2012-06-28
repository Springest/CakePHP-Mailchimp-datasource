<?php
class MailchimpSubscriber extends AppModel {
	public $useDbConfig = 'mailchimp';
	public $name = 'MailchimpSubscriber';
	public $useTable = false;

	// $validate is really defined in the __construct constructor because of
	// i18n issues
	public $validate = array();

	/**
	 * The basic Mailchimp schema
	 */
	public $_schema = array(
		'id' => array(
			'type' => 'int',
			'null' => true,
			'key' => 'primary',
			'length' => 11,
		),
		'emailaddress' => array(
			'type' => 'string',
			'null' => false,
			'length' => 256
		),
		'FNAME' => array(
			'type' => 'string',
			'null' => true,
			'key' => 'primary',
			'length' => 128
		),
		'LNAME' => array(
			'type' => 'string',
			'null' => true,
			'length' => 128
		),
		'GENDER' => array(
			'type' => 'string',
			'null' => true,
			'length' => 32
		),
	);

	/**
	 * Override Model::delete, because it would block deleting when
	 * useTable = false and no records exists
	 *
	 * @param <type> $id
	 * @param <type> $cascade
	 * @return <type>
	 */
	function delete($id = null, $cascade = true) {
		if (!empty($id)) {
			$this->id = $id;
		}
		$id = $this->id;

		if ($this->beforeDelete($cascade)) {
			$db =& ConnectionManager::getDataSource($this->useDbConfig);
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

	function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$this->validate = array(
			'emailaddress' => array(
				'rule' => array('email'),
				'message' => __("Please enter a valid e-mail address", true)
			)
		);
	}
}
?>