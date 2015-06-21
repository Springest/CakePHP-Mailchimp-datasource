<?php

class NewsletterSubscriberFixture extends CakeTestFixture {

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
		'email' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 50, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array()
	);

	public $records = array(
		array(
			'id' => 1,
			'email' => 'test@testdomaim.com'
		),
	);

}
