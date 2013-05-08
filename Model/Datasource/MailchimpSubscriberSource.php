<?php
/**
 * Datasource for Mailchimp
 *
 * Used for saving, selecting and deleting Subscribers
 *
 * Example URL:
 * http://api.mailchimp.com/1.2/?method=listSubscribe&apikey=<apikey>&id=
 * <list id>&email_address=<email_address>&merge_vars[FNAME]=Firstname&
 * merge_vars[LNAME]=Lastname&merge_vars[INTERESTS]=Dogs,Cats,Shoes&output=json
 *
 * Use the $_schema in the model to supply custom values. The names must
 * coincide with the values you created in mailchimp
 */

App::uses('HttpSocket', 'Network/Http');

class MailchimpSubscriberSource extends DataSource {

	public $description = "A datasource for the Mailchimp API";

	/**
	 * Our default config options. These options can be customized in
	 * app/Config/database.php and will be merged in the __construct().
	 *
	 * apikey  	The key that is needed by MailChimp to connect to the API.
	 * 			Keys can be requested in Mailchimp's control panel.
	 * listId 	The list that is used for adding/reading/deleting subscribers in.
	 * 			List id can be found by executing a lists method to mailchimp.
	 * baseUrl	The url that is used for connecting to the Mailchimp API.
	 * 			This depends on the Mailchimp API version that is used.
	 */
	public $config = array(
		'apikey'  => '',
		'listId'  => '',
		'baseUrl' => ''
  	);

	/**
	 * Construct our Datasource Class
	 * @param <Array> $config
	 */
	public function __construct($config) {
		$this->connection = new HttpSocket();
		parent::__construct($config);
	}

	/**
	 * ListSources()
	 *
	 * Required by CakePHP
	 *
	 * listSources() is for caching. You'll likely want to implement caching in
 	 * your own way with a custom datasource. We havent and thus return null.
	 *
	 * @return null
	 */
	public function listSources() {
		return null;
	}

	/**
	 * describe()
	 *
	 * Required by CakePHP
	 *
	 * describe() tells the model your schema for Model::save()
	 *
	 * @param <Model> $model
	 * @return <Array>
	 */
	public function describe(Model $Model) {
		return $Model->_schema;
	}

	/**
	 * Find a subscriber
	 *
	 * @param object $model
	 * @param array $queryData
	 * @return boolean
	 */
	public function read(Model $Model, $data = array()) {
		$url = $this->buildUrl('listMemberInfo', $data['conditions']['emailaddress']);
		$response = json_decode($this->connection->get($url), true);

		if (is_null($response)) {
			$error = json_last_error();
			throw new CakeException($error);
		}

		return array($Model->alias => $response);
	}

	/**
	 * Add a subscriber to the list
	 *
	 * @param object $model
	 * @param array $fields
	 * @param array $values
	 * @return boolean
	 */
	public function create(Model $Model, $fields = array(), $values = array()) {
		$data = array_combine($fields, $values);

		# Extract email address from $data so we don't submit it twice
		$emailaddress = $data['emailaddress'];
		unset($data['emailaddress']);
		$url = $this->buildUrl('listSubscribe', $emailaddress, $data );
		$response = json_decode($this->connection->get($url), true);

		if (is_null($response)) {
			$error = json_last_error();
			throw new CakeException($error);
		}

		return true;
	}

	/**
	 * Updates a subscriber on the list
	 *
	 * @param object $model
	 * @param array $fields
	 * @param array $values
	 * @return boolean
	 */
	public function update(Model $Model, $fields = array(), $values = array()) {
		return $this->create($Model, $fields, $values);
	}

	/**
	 * Delete a subscriber from the list
	 *
	 * @param object $model
	 * @param string $id either emailaddress or id of the subscriber
	 * @return boolean
	 */
	public function delete(Model $Model, $conditions = null) {
		// We can use $id instead of $emailaddress here, this is allowed as per
		// Mailchimp's API docs
		$url = $this->buildUrl('listUnsubscribe', $conditions[$Model->alias . '.id']);

		$response = json_decode($this->connection->get($url), true);

		if (is_null($response)) {
			$error = json_last_error();
			throw new CakeException($error);
		}

		return true;
	}

	/**
	 * Build the URL that is used for sending a request
	 *
	 * @param string $method the method of the mailchimp API that will be used. For a full listing of methods, see http://www.mailchimp.com/api/rtfm/
	 * @param string $emailAddress an email-address or id that will be added/deleted or retrieved
	 * @param array $data an array of key value-pairs that will be used as merge_vars (the custom fields created in mailchimp)
	 * @param string $apiKey the key that will be used for authentication against Mailchimps' API
	 * @param string $listId the id of the list that the request will be run against; set in database.php
	 * @param string $output the type of response that will be outputted; this datasource works with json only
	 * @return string $url	the url that will be used to do request to Mailchimp
	 */
	private function buildUrl($method, $emailaddress, $data = array(), $apikey = null, $listId = null, $output = "json") {
		if(empty($apikey)) { $apikey = $this->config['apikey']; }
		if(empty($listId)) { $listId = $this->config['listId']; }

		$url  = $this->config['baseUrl'];
		$url .= "?method=".$method;
		$url .= "&email_address=".$emailaddress;
		$url .= "&output=".$output;
		$url .= "&apikey=".$apikey;
		$url .= "&id=".$listId;

		foreach($data as $key => $value) {
			$url .= "&merge_vars[".$key."]=".$value;
		}

		return $url;
	}
}
?>