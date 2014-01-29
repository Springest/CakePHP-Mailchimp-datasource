# Cakephp Plugin for Mailchimp

With this datasource you can easily add users to your Mailchimp newsletter. It works with CakePHP 2.0+.

Please check [the original blogpost][1] on our devblog for more background information.

It uses the current API version 2.0 from Mailchimp.

## Setup

Copy the plugin into the `APP/Plugin` folder and make sure it is loaded using `CakePlugin::loadAll()`, for example.

Add the `$mailchimp` datasource to `APP/Config/database.php`

	public $mailchimp = array(
		'datasource' => 'Mailchimp.MailchimpSubscriberSource',
		'apikey' => 'YOUR_API_KEY',
		'defaultListId' => 'YOUR_LIST_ID',
	);

For BC you can also use the Configure class to set the API data:

	$config['Mailchimp'] = array(
		'apiKey' => 'YOUR_API_KEY',
		'defaultListId' => 'YOUR_LIST_ID',
	);

## Usage

Include the Model where you need it via

    $this->MailchimpSubscriber = ClassRegistry::init('Mailchimp.MailchimpSubscriber');

When you've set the datasource up correctly, you will now be able to do stuff like `$this->MailchimpSubscriber->save($this->request->data)`,
or call other regular Model methods (like `Model::find`) from any controller that uses the `MailchimpSubscriber` model.

[1]: http://devblog.springest.com/mailchimp-datasource-cakephp

## Debugging

Unfortunately, the 2.0 vendor class from Mailchimp does not through exceptions. So if your methods return false and you need to know
the error message/code you will have to use the following:

	debug($this->MailchimpSubscriber->response);

with `$this->MailchimpSubscriber` being your model.

## Dependencies

Possibly my Tools plugin for the admin backend (optional)


## Disclaimer

MIT Licence

2013 Mark Scherer: Upgraded Mailchimp API from 1.3 to 2.0
