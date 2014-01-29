<?php
App::uses('MailchimpAppModel', 'Mailchimp.Model');
App::uses('HttpSocket', 'Network/Http');

class MailchimpExport extends MailchimpAppModel {

	/**
	 * Exports/dumps members of a list and all of their associated details.
	 *
	 * Optional params:
	 * `status` - The status to get members for - one of (subscribed, unsubscribed, cleaned), defaults to subscribed
	 * `segment` - Pull only a certain Segment of your list
	 * `since` - Only return member whose data has changed since a GMT timestamp - in YYYY-MM-DD HH:mm:ss format
	 *
	 * @param array $params
	 * @return array Result
	 */
	public function exportMembers($params = array()) {
		return $this->_get('list', $params);
	}

	/**
	 * Exports/dumps all Subscriber Activity for the requested campaign.
	 *
	 * Optional params:
	 * `include_empty` - If set to "true" a record for every email address sent to will be returned even if there is no activity data. defaults to "false"
	 * `since` - Only return activity recorded since a GMT timestamp - in YYYY-MM-DD HH:mm:ss format
	 *
	 * @param array $params
	 * @return array Result
	 */
	public function exportActivity($params = array()) {
		$params['id'] = Configure::read('Mailchimp.defaultCampaignId');
		return $this->_get('campaignSubscriberActivity', $params);
	}

	/**
	 * Make the export query
	 *
	 * @param string $type
	 * @param array $params Query strings
	 * @return array Result
	 * @throws CakeException
	 */
	protected function _get($type, $params) {
		$url = 'http://:dc.api.mailchimp.com/export/1.0/';
		$apiKey = Configure::read('Mailchimp.apiKey');
		$dc = substr($apiKey, strpos($apiKey, '-') + 1);
		$url = String::insert($url, array('dc' => $dc));

		$params += array('apikey' => $apiKey, 'id' => Configure::read('Mailchimp.defaultListId'));
		$url .= $type . '/';

		$Socket = new HttpSocket();
		$response = $Socket->get($url, $params);
		if ($response->body) {
			$result = array();
			$lines = explode("\n", trim($response->body));
			foreach ($lines as $line) {
				$result[] = json_decode($line, true);
			}
			if (!empty($result[0]['error'])) {
				throw new MailchimpException('Error ' . $result[0]['code'] . ': ' . $result[0]['error']);
			}
			return $result;
		}
		return array();
	}

}
