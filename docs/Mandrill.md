# Mandrill
This plugin uses v1 of Mandrill API: https://mandrillapp.com/api/docs/


## Configuration
You can configure it using Configure and the key `Mandrill`:
```php
// in your config.php file
$config = array(
	'Mandrill' => array(
		'apiKey' => ...
	)
);
```
You can also overwrite it from anywhere:
```php
Configure::write('Mandrill.apiKey', 'rQLMYhItRJoKhtr8rs1uig');
```

## Usage of the Lib

Get list of senders:
```php
App::uses('MandrillLib', 'Mailchimp.Lib');

$this->Mandrill = new MandrillLib();
$senders = $this->Mandrill->call('senders/list');
```

## Usage of the transport class
The MandrillTransport is for sending mails it via CakeEmail.

Set `'transport' => 'Mandrill.Mandrill'` in your CakeEmail configs.
