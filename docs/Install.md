# Installation

## How to include
Installing the Plugin is pretty much as with every other CakePHP Plugin.

Put the files in `Plugin/Mailchimp`, using Packagist/Composer:
```
composer require dereuromark/cakephp-mailchimp:dev-dev
```

which will add the following to your `composer.json` file:

```
"require": {
	"dereuromark/cakephp-mailchimp": "dev-dev"
}
```

Details @ https://packagist.org/packages/dereuromark/cakephp-mailchimp

This will load the plugin (within your boostrap file):
```php
Plugin::load('Mailchimp');
```
or
```php
Plugin::loadAll(...);
```
