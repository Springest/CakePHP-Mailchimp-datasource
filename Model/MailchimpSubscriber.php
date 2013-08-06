<?php

App::uses('MailchimpAppModel', 'Mailchimp.Model');

class MailchimpSubscriber extends MailchimpAppModel {

	public $validate = array(
		'email' => array(
			'email' => array(
				'rule' => array('email'),
				'message' => 'Please enter a valid e-mail address')));

	/**
	 * Use $_schema to set any mailchimp fields that you want to use
	 *
	 * @var array
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
			'length' => 256),
		'fname' => array(
			'type' => 'string',
			'null' => true,
			'key' => 'primary',
			'length' => 128),
		'lname' => array(
			'type' => 'string',
			'null' => true,
			'length' => 128),
		);

	/**
	 * Subscribe email address with optional additional data.
	 *
	 * Note that it will automatically convert field names:
	 * 'some_code' etc => 'somecode' (without underscores).
	 *
	 * @param array $queryData
	 * - email (required)
	 * - id (optional, defaults to default id)
	 * - all other fields
	 * @param array $options
	 * - emailType
	 * - doubleOptin
	 * - updateExisting
	 * - replaceInterests
	 * - sendWelcome
	 * @return boolean Success
	 * @throws CakeException When length of merge var (10) is exceeded.
	 */
	public function subscribe($queryData, $options = array()) {
		$id = $this->settings['defaultListId'];
		if (isset($queryData['id'])) {
			$id = $queryData['id'];
			unset($queryData['id']);
		}
		$email = $queryData['email'];
		unset($queryData['email']);

		foreach ($queryData as $key => $value) {
		if (strpos($key, '_') === false) {
			if (strlen($key) > 10) {
				throw new CakeException('Max length for merge vars is 10');
			}
			continue;
		}
		$newKey = str_replace('_', '', $key);
		if (strlen($newKey) > 10) {
				throw new CakeException('Max length for merge vars is 10');
			}
			$queryData[$newKey] = $value;
			unset($queryData[$key]);
		}

		$defaults = array(
			'emailType' => 'html',
			'doubleOptin' => true,
			'updateExisting' => false,
			'replaceInterests' => true,
			'sendWelcome' => false
		);
		$options += $defaults;
		extract($options);

		$response = $this->Mailchimp->listSubscribe($id, $email, $queryData, $emailType, $doubleOptin, $updateExisting, $replaceInterests, $sendWelcome);
		return $response;
	}

	/**
	 * Unsubscribe email address.
	 *
	 * @param array $queryData
	 * - email (required)
	 * - id (optional, defaults to default id)
	 * @param array $options
	 * - deleteMember
	 * - sendGoodbye
	 * - sendNotify
	 * @return boolean Success
	 */
	public function unsubscribe($queryData, $options = array()) {
		$id = $this->settings['defaultListId'];

		$defaults = array(
			'deleteMember' => false,
			'sendGoodbye' => true,
			'sendNotify' => true
		);
		$options += $defaults;
		extract($options);

		$response = $this->Mailchimp->listUnsubscribe($id, $queryData['email'], $deleteMember, $sendGoodbye, $sendNotify);
		return $response;
	}

	/**
	 * Get all of the list members for a list that are of a particular status. Are you trying to get a dump including lots of merge
	 * data or specific members of a list? If so, checkout the <a href="/export">Export API</a>

	 * @param string $id the list id to connect to. Get by calling lists()
	 * @param string $status the status to get members for - one of(subscribed, unsubscribed, <a target="_blank" href="http://eepurl.com/gWOO">cleaned</a>, updated), defaults to subscribed
	 * @param string $since optional pull all members whose status (subscribed/unsubscribed/cleaned) has changed or whose profile (updated) has changed since this date/time - 24 hour format in <strong>GMT</strong>, eg "2013-12-30 20:30:00"
	 * @param int $start optional for large data sets, the page number to start at - defaults to 1st page of data (page 0)
	 * @param int $limit optional for large data sets, the number of results to return - defaults to 100, upper limit set at 15000
	 * @param string $sort_dir optional ASC for ascending, DESC for descending. defaults to ASC even if an invalid value is encountered.
	 * @return array Array of a the total records match and matching list member data for this page (see Returned Fields for details)
	 * int total the total matching records
	 * array data the data for each member, including:
	 * string email Member email address
	 * date timestamp timestamp of their associated status date (subscribed, unsubscribed, cleaned, or updated) in GMT
	 * string reason For unsubscribes only - the reason collected for the unsubscribe. If populated, one of 'NORMAL','NOSIGNUP','INAPPROPRIATE','SPAM','OTHER'
	 * string reason_text For unsubscribes only - if the reason is OTHER, the text entered.
	 */
	public function listMembers($status = 'subscribed', $since = null, $start = 0, $limit = 100, $sort_dir = 'ASC', $id = null) {
		if (!$id) {
			$id = $this->settings['defaultListId'];
		}
		return $this->Mailchimp->listMembers($id, $status, $since, $start, $limit, $sort_dir);
	}

	/**
	 * Search account wide or on a specific list using the specified query terms
	 *
	 * @param string $query terms to search on, <a href="http://kb.mailchimp.com/article/i-cant-find-a-recipient-on-my-list" target="_blank">just like you do in the app</a>
	 * @param string $id optional the list id to limit the search to. Get by calling lists()
	 * @param int offset optional the paging offset to use if more than 100 records match
	 * @return array An array of both exact matches and partial matches over a full search
	 * array exact_matches
	 * int total total members matching
	 * array members each entry will match the data format for a single member as returned by listMemberInfo()
	 * array full_search
	 * int total total members matching
	 * array members each entry will match the data format for a single member as returned by listMemberInfo()
	 */
	public function search($query, $offset = 0, $id = null) {
		if (!$id) {
			$id = $this->settings['defaultListId'];
		}
		return $this->Mailchimp->searchMembers($query, $id, $offset);
	}

	/**
	 * Retrieve all List Ids a member is subscribed to.
	 *
	 * @param string $email_address the email address to check OR the email "id" returned from listMemberInfo, Webhooks, and Campaigns
	 * @return array An array of list_ids the member is subscribed to.
	 */
	public function listsForEmail($email_address) {
		return $this->Mailchimp->listsForEmail($email_address);
	}

	/**
	 * Retrieve all Campaigns Ids a member was sent
	 *
	 * @param string $email_address the email address to unsubscribe  OR the email "id" returned from listMemberInfo, Webhooks, and Campaigns
	 * @param array $options optional extra options to modify the returned data.
	 * string list_id optional A list_id to limit the campaigns to
	 * bool   verbose optional Whether or not to return verbose data (beta - this will change the return format into something undocumented, but consistent). defaults to false
	 * @return array An array of campaign_ids the member received
	 */
	public function campaignsForEmail($email_address, array $options = array()) {
		return $this->Mailchimp->campaignsForEmail($email_address, $options);
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
