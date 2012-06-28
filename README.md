# Cakephp Datasource for Mailchimp

With this datasource you can easily add users to your Mailchimp newsletter. It works in both CakePHP 1.2+ and CakePHP 2.0+, but be sure to check out the right branch. The `master` branch is the active branch which supports CakePHP 2.0 onwards. For older CakePHP versions, please check out the `cake-1.3` branch (despite the name it should work with CakePHP 1.2 too).

Please check [the original blogpost][1] on our devblog for more background information.

## Setup
### CakePHP 1.2+
Add the model and datasource in their appropriate folders. 

* `APP_DIR/models/`
* `APP_DIR/models/datasources/`

Add the `$mailchimp` datasource to `APP_DIR/config/database.php`

    var $mailchimp = array(
        'datasource' => 'mailchimp_subscriber',
        'apiKey' => 'YOUR_API_KEY',
        'defaultListId' => 'YOUR_LIST_ID',
        'baseUrl' => 'http://us1.api.mailchimp.com/1.2/' // or another one, depending on the API version you use
    );
    
### CakePHP 2.0+
Add the model and datasource in their appropriate folders. 

* `APP_DIR/Model/`
* `APP_DIR/Model/Datasource/`

Add the `$mailchimp` datasource to `APP_DIR/config/database.php`

	public $mailchimp = array(
		'datasource' => 'MailchimpSubscriberSource',
		'apikey' => 'YOUR_API_KEY',
		'listId' => 'YOUR_LIST_ID',
		'baseUrl' => 'http://us1.api.mailchimp.com/1.2/' // or another one, depending on the API version you use
	);

## Usage
When you've set the datasource up correctly, you will now be able to do stuff like `$this->MailchimpSubscriber->save($this->data)`, or call other regular Model methods (like `Model::find`) from any controller that uses the `MailchimpSubscriber` model.

[1]: http://devblog.springest.com/mailchimp-datasource-cakephp