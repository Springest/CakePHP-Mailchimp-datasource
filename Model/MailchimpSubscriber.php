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
	 * @see http://apidocs.mailchimp.com/api/2.0/lists/subscribe.php
	 *
	 * @param array $queryData
	 * - email (required)
	 * - all other fields
	 * @param array $options
	 * - id (optional, defaults to default id)
	 * - emailType
	 * - doubleOptin
	 * - updateExisting
	 * - replaceInterests
	 * - sendWelcome
	 * @param array $mergeVars Vars to be added (will to be modified, need to be snake case)
	 * - new-email
	 * - groupings
	 * - optin_ip
	 * - optin_time
	 * - mc_location
	 * - mc_language
	 * - mc_notes
	 * @return bool Success
	 * @throws MailchimpException When length of merge var (10) is exceeded.
	 */
	public function subscribe(array $queryData, array $options = array(), array $mergeVars = array()) {
		foreach ($queryData as $key => $value) {
			if (strpos($key, '_') === false) {
				if (strlen($key) > 10) {
					throw new MailchimpException('Max length for merge vars is 10');
				}
				continue;
			}
			$newKey = str_replace('_', '', $key);
			if (strlen($newKey) > 10) {
				throw new MailchimpException('Max length for merge vars is 10');
			}
			$queryData[$newKey] = $value;
			unset($queryData[$key]);
		}
		$options['email'] = $queryData['email'];
		unset($queryData['email']);

		$defaults = array(
			'id' => $this->settings['defaultListId'],
			'emailType' => 'html',
			'doubleOptin' => true,
			'updateExisting' => false,
			'replaceInterests' => true,
			'sendWelcome' => false
		);
		$options += $defaults;
		$options['merge_vars'] = $queryData + $mergeVars;

		if (is_string($options['email'])) {
			$options['email'] = array('email' => $options['email']);
		}

		return $this->call('lists/subscribe', $options);
	}

	/**
	 * Unsubscribe email address.
	 *
	 * @see http://apidocs.mailchimp.com/api/2.0/lists/unsubscribe.php
	 *
	 * @param array $queryData
	 * - email (required)
	 * @param array $options
	 * - id (optional, defaults to default id)
	 * - deleteMember
	 * - sendGoodbye
	 * - sendNotify
	 * @return bool Success
	 */
	public function unsubscribe(array $queryData, array $options = array()) {
		$defaults = array(
			'id' => $this->settings['defaultListId'],
			'deleteMember' => false,
			'sendGoodbye' => true,
			'sendNotify' => true
		);
		$options += $defaults;

		if (is_string($queryData['email'])) {
			$queryData['email'] = array('email' => $queryData['email']);
		}
		$options = $queryData + $options;

		return $this->call('lists/unsubscribe', $options);
	}

	/**
	 * Subscribe a batch of email addresses to a list at once.
	 * Maximum batch sizes vary based on the amount of data in each record,
	 * though you should cap them at 5k - 10k records, depending on your experience.
	 * These calls are also long, so be sure you increase your timeout values.
	 *
	 * $emails should be an array of arrays, containing at least "email" as key.
	 * "type" (html/text) is optional.
	 *
	 * @see http://apidocs.mailchimp.com/api/2.0/lists/batch-subscribe.php
	 *
	 * @param array $emails List of emails (either flat or deep)
	 * @param array $options
	 * - emailType
	 * - doubleOptin
	 * - updateExisting
	 * - replaceInterests
	 * @return array
	 */
	public function batchSubscribe(array $emails, array $options = array()) {
		$defaults = array(
			'id' => $this->settings['defaultListId'],
			'emailType' => 'html',
			'doubleOptin' => true,
			'updateExisting' => false,
			'replaceInterests' => true,
		);
		$options += $defaults;

		$batch = array();
		foreach ($emails as $email) {
			if (is_string($email)) {
				$email = array(
					'email' => $email
				);
			}
			if (is_string($email['email'])) {
				$email['email'] = array('email' => $email['email']);
			}
			if (empty($email['email_type'])) {
				$email['email_type'] = $options['emailType'];
			}
			$batch[] = $email;
		}
		$options['batch'] = $batch;

		$result = $this->call('lists/batch-subscribe', $options);
		if (!isset($result['success_count']) && isset($result['add_count']) && isset($result['update_count'])) {
			$result['success_count'] = $result['add_count'] + $result['update_count'];
		}
		return $result;
	}

	/**
	 * MailchimpSubscriber::batchUnsubscribe()
	 *
	 * @see http://apidocs.mailchimp.com/api/2.0/lists/batch-unsubscribe.php
	 *
	 * @param array $emails List of emails (either flat or deep)
	 * @param array $options
	 * - deleteMember
	 * - sendGoodbye
	 * - sendNotify
	 * @return array
	 */
	public function batchUnsubscribe(array $emails, array $options = array()) {
		$defaults = array(
			'id' => $this->settings['defaultListId'],
			'deleteMember' => false,
			'sendGoodbye' => true,
			'sendNotify' => true
		);
		$options += $defaults;
		foreach ($emails as $key => $email) {
			if (is_string($email)) {
				$emails[$key] = array(
					'email' => $email
				);
			}
		}
		$options['batch'] = $emails;

		return $this->call('lists/batch-unsubscribe', $options);
	}

}
