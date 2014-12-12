# CakePHP Plugin for Mailchimp

[![Build Status](https://api.travis-ci.org/dereuromark/cakephp-mailchimp.png?branch=dev)](https://travis-ci.org/dereuromark/cakephp-mailchimp)
[![License](https://poser.pugx.org/dereuromark/cakephp-mailchimp/license.png)](https://packagist.org/packages/dereuromark/cakephp-mailchimp)
[![Total Downloads](https://poser.pugx.org/dereuromark/cakephp-mailchimp/d/total.png)](https://packagist.org/packages/dereuromark/cakephp-mailchimp)

With this datasource you can easily add users to your Mailchimp newsletter. It works with CakePHP 2.x.

Please check [the original blogpost][1] on our devblog for more background information.

It uses the current API version 2.0 from Mailchimp.

## Setup

Install the plugin into the `APP/Plugin` folder, ideally via composer.

Make sure it is loaded - using `CakePlugin::loadAll()`, for example.

Use the Configure class to set the API data (via `APP/Config/configs.php` etc):
```php
$config['Mailchimp'] = array(
	'apiKey' => 'YOUR_API_KEY',
	'defaultListId' => 'YOUR_LIST_ID',
	'defaultCampaignId => 'YOUR_CAMPAIGN_ID'
);
```

Don't forget to include that configs file in your `bootstrap.php`:

	Configure::load('configs');

## Usage

Include the Model where you need it via
```php
$this->Mailchimp = ClassRegistry::init('Mailchimp.Mailchimp');

$this->MailchimpSubscriber = ClassRegistry::init('Mailchimp.MailchimpSubscriber');

$this->MailchimpCampaign = ClassRegistry::init('Mailchimp.MailchimpCampaign');
```
Either use the available wrapper functionality or directly invoke `call()` on the models;

### Usage of Subscriber datasource

Warning: For the subscriptions there is also a datasource approach available.
This is not yet fully tested/working for API 2.0, though.

Add the `$mailchimp` datasource to `APP/Config/database.php`
```php
public $mailchimp = array(
	'datasource' => 'Mailchimp.MailchimpSubscriberSource',
	'apikey' => 'YOUR_API_KEY', // Optional, I prefer using Configure
	'defaultListId' => 'YOUR_LIST_ID', // Optional, I prefer using Configure
);
```
When you've set the datasource up correctly, you will now be able to do stuff like `$this->MailchimpSubscriber->save($this->request->data)`,
or call other regular Model methods (like `Model::find`) from any controller that uses the `MailchimpSubscriber` model.

[1]: http://devblog.springest.com/mailchimp-datasource-cakephp

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

## Dependencies

Possibly my Tools plugin for the admin backend (optional)


## Mandrill
This plugin now also contains some Mandrill API wrapper as well as MandrillTransport for sending it via CakeEmail.
This requires
```php
	$config['Mandrill'] = array(
		'apiKey' => 'YOUR_API_KEY',
	);
```
## Disclaimer
2013/2014: Upgraded Mailchimp API from 1.3 to 2.0
