# Mailchimp
This plugin uses v2 of Mailchimp API: https://apidocs.mailchimp.com/api/2.0/


## Configuration
You can configure it using Configure and the key `Mailchimp`:
```php
// in your config.php file
$config = array(
	'Mailchimp' => array(
		'apiKey' => ...,
		'defaultListId' => ...,
		'defaultCampaignId' => ...
	)
);
```

Don't forget to include that configs file in your `bootstrap.php`:
```php
// When you use configs.php
Configure::load('configs');
```

You can also overwrite it from anywhere:
```php
Configure::write('Mailchimp.defaultListId', '123');
Configure::write('Mailchimp.apiKey', '456-us3');
Configure::write('Mailchimp.defaultCampaignId', '789');
```
This is useful when (e.g. in debug mode) you only want to test it or write
to a test list.


## Usage
Include any model you need via `ClassRegistry::init('Mailchimp.{ModelName}')`:
```php
$this->Mailchimp = ClassRegistry::init('Mailchimp.Mailchimp');

$this->MailchimpSubscriber = ClassRegistry::init('Mailchimp.MailchimpSubscriber');

$this->MailchimpCampaign = ClassRegistry::init('Mailchimp.MailchimpCampaign');
```

In case you want to manually query something, use
```
$this->{ModelName}->call('namespace/action-name', $options);
```
But each model has already convenience wrappers:

### Mailchimp model
The following methods can directly be used on the model:
- getAccountDetails()
- getVerifiedDomains()
- listActivity()

and more...

### MailchimpSubscriber
- subscribe()
- unsubscribe()
- batchSubscribe()
- batchUnsubscribe()

### MailchimpExport
- exportMembers()
- exportActivity()

### MailchimpCampaign
- campaigns()
- search()

and more...


## Subscriber Example
Let's say we have a subscriber contact form:
```html
<?php echo $this->Form->create('Contact'); ?>
<?php echo $this->Form->input('email'); ?>
<?php	echo $this->Form->submit(__('Sign in now'), array('class' => 'btn btn-primary')); ?>
<?php echo $this->Form->end(); ?>
```

Then we can have this in the controller action:
```php
$MailchimpSubscriber = ClassRegistry::init('Mailchimp.MailchimpSubscriber');
$data = $this->request->data['Contact'];
$data['source'] = 'contactform';
$options = array('doubleOptin' => true, 'updateExisting' => false);

$response = $MailchimpSubscriber->subscribe($data, $options);
// Act accordingly
```


## Debugging

Unfortunately, the 2.0 vendor class from Mailchimp does not through exceptions by itself. So if your methods return false and you need to know
the error message/code you will have to use the following:
```php
debug($this->MailchimpSubscriber->response);
```
with `$this->MailchimpSubscriber` being your model.

You can, however, make the plugin throw exceptions using
```php
	Configure::write('Mailchimp.exceptions', true);
```
This will then throw a `MailchimpException` you can catch, log away and continue in your code.

## Testing
By default all tests use mocks. This is useful to prevent real API connections for large scale testing as
in travis for example.
But once in a while you might want to manually asserr that the mocks still represent the actual API responses.
For that the test cases have been adjusted to allow such a non-mocked test-run.

Use `&debug=1` via WebTestRunner or `-v` via CLI to have real life connections. Note that you must
have configured it using a valid API key then.
