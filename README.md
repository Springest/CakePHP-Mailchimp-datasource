# Cakephp Plugin for Mailchimp

With this datasource you can easily add users to your Mailchimp newsletter. It works with CakePHP 2.0+.

Please check [the original blogpost][1] on our devblog for more background information.


## Setup

Copy the plugin into the `APP/Plugin` folder and make sure it is loaded using `CakePlugin::loadAll()`, for example.

Add the `$mailchimp` datasource to `APP_DIR/Config/database.php`

	public $mailchimp = array(
		'datasource' => 'Mailchimp.MailchimpSubscriberSource',
		'apikey' => 'YOUR_API_KEY',
		'listId' => 'YOUR_LIST_ID',
		'baseUrl' => 'http://us1.api.mailchimp.com/1.2/' // or another one, depending on the API version you use
	);


## Usage

Include the Model where you need it via

    $this->MailchimpSubscriber = ClassRegistry::init('Mailchimp.MailchimpSubscriber');

When you've set the datasource up correctly, you will now be able to do stuff like `$this->MailchimpSubscriber->save($this->request->data)`, or call other regular Model methods (like `Model::find`) from any controller that uses the `MailchimpSubscriber` model.

[1]: http://devblog.springest.com/mailchimp-datasource-cakephp


## Dependencies

Possibly my Tools plugin for the admin backend (optional)


## Disclaimer

MIT Licence

Modified 2012 Mark Scherer: added tests + some basic admin backend and made it a plugin
