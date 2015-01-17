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
You can also overwrite it from anywhere:
```php
Configure::write('Mailchimp.defaultListId', '123');
Configure::write('Mailchimp.apiKey', '456-us3');
Configure::write('Mailchimp.defaultCampaignId', '789');
```
This is useful when (e.g. in debug mode) you only want to test it or write
to a test list.


## Usage
Include any model you need via `ClassRegistry::init('Mailchimp.{ModelName}')`.

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
