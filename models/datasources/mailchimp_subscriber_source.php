<?php
/**
 * Datasource for Mailchimp
 *
 * Used for saving, selecting and deleting Subscribers
 *
 * Example URL:
 * http://api.mailchimp.com/1.2/?method=listSubscribe&apikey=<apikey>&id=<list id>&email_address=<email_address>&merge_vars[FNAME]=Firstname&merge_vars[LNAME]=Lastname&merge_vars[INTERESTS]=Dogs,Cats,Shoes&output=json
 *
 * Use the $_schema in the model to supply custom values. The names must coincide with the values you created in mailchimp
 */
App::import('Core', 'HttpSocket');
class MailchimpSubscriberSource extends DataSource {

	/**
	 * The key that is needed by MailChimp to connect to the API.
	 * Keys can be requested in Mailchimp's control panel.
	 * This value is populated from the database.php config file.
	 * @var <string> $apiKey
	 */
	var $apiKey;

	/**
	 * The list that is used for adding/reading/deleting subscribers in.
	 * List id can be found by executing a lists method to mailchimp.
	 * This value is populated from the database.php config file.
	 * @var <string> $listId
	 */
	var $listId;

	/**
	 * The url that is used for connecting to the Mailchimp API.
	 * This depends on the Mailchimp API version that is used.
	 * This value is populated from the database.php config file.
	 * @var <string> $baseUrl
	 */
	var $baseUrl;

	/**
	 * Construct our Datasource Class
	 * @param <type> $config
	 */
	public function __construct($config) {

		//set ApiKey, default list Id and baseUrl
		$this->apiKey	= $config['apiKey'];
		$this->listId	= $config['defaultListId'];
		$this->baseUrl	= $config['baseUrl'];

		//create socket connection
		$this->connection = new HttpSocket();
		parent::__construct($config);
	}

	/**
	 * ListSources()
	 *
	 * Required by CakePHP
	 * @return <type>
	 */
	public function listSources() {
		return array('Mailchimp');
	}

	/**
	 * describe()
	 *
	 * Required by CakePHP
	 * @param <type> $model
	 * @return <type>
	 */
	function describe($model) {
		return $this->_schema['Mailchimp'];
	}

	/**
	 * Find a subscriber
	 *
	 * @param object $model
	 * @param array $queryData
	 * @return boolean
	 */
	function read($model, $queryData = array()) {

		$url = $this->buildUrl('listMemberInfo', $queryData['conditions']['emailaddress']);

		$response = json_decode($this->connection->get($url), true);

		if(isset($response['error'])) {
			return false;
		}

		return $response;
	}

	/**
	 * Add a subscriber to the list
	 *
	 * @param object $model
	 * @param array $fields
	 * @param array $values
	 * @return boolean
	 */
	function create($model, $fields = array(), $values = array()) {

		$data = array_combine($fields,$values);

		$emailaddress = $data['emailaddress'];
		unset($data['emailaddress']);

		if(isset($emailaddress) )  {
			//build mailchimp request url using $data array send from controller
			$url = $this->buildUrl('listSubscribe', $emailaddress, $data );
		}

		$result = json_decode($this->connection->get($url), true);

		if(isset($result['error'])) {
			return false;
		}

		return true;
	}

	/**
	 * Delete a subscriber from the list
	 *
	 * @param object $model
	 * @param string $id either emailaddress or id of the subscriber
	 * @return boolean
	 */
	public function delete(&$model, $id = null)	{

		//we can use $id instead of $emailadres here, this is allowed as per Mailchimp's API docs,
		$url = $this->buildUrl('listUnsubscribe', $model->id );

		$result = json_decode($this->connection->get($url), true);
		
		if(isset($result['error']) ) {
			return false;
		}
		return true;
	}

	 /**
	  * Build the URL that is used for sending a request
	  *
	  * @param string $method the method of the mailchimp API that will be used
	  * @param string $emailAddress an email-address or id that will be added/deleted or retrieved
	  * @param array $data an array of key value-pairs that will be used as merge_vars (the custom fields created in mailchimp)
	  * @param string $apiKey the key that will be used for authentication against Mailchimps' API
	  * @param string $listId the id of the list that the request will be run against; set in database.php
	  * @param string $output the type of response that will be outputted; this datasource works with json only
	  * @return string $url	the url that will be used to do request to Mailchimp
	  */
	public function buildUrl($method, $emailAddress, array $data = array(), $apiKey = null, $listId = null, $output = "json" )
	{
		//set the baseUrl from the class
		$url  = $this->baseUrl;

		//set the method of the request, for a full listing of methods, see http://www.mailchimp.com/api/rtfm/
		$url .= "?method=".$method;

		//set the emailaddress or ID of the emailadres (for deleting)
		$url .= "&email_address=".$emailAddress;

		//set the output, default is JSON
		$url .= "&output=".$output;

		//if API key not set, use the one from the class, this is usable for overriding the API key if necessary
		if(empty($apiKey))	{ $url .= "&apikey=".$this->apiKey; }
		else				{ $url .= "&apikey=".$apiKey; }

		//same goes for the list_id
		if(empty($listId))	{ $url .= "&id=".$this->listId;  }
		else				{ $url .= "&id=".$listId; }

		//for custom values ...
		foreach($data as $key => $value) {
			$url .= "&merge_vars[".$key."]=".$value;
		}

		return $url;
	}
}
?>