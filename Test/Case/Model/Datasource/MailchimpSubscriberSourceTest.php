<?php

App::uses('MailchimpSubscriberSource', 'Mailchimp.Model/Datasource');
App::uses('MyCakeTestCase', 'Tools.TestSuite');

class MailchimpSubscriberSourceTest extends MyCakeTestCase {

	public $MailchimpSubscriberSource;

	public $fixtures = array('plugin.Mailchimp.NewsletterSubscriber');

	public function setUp() {
		parent::setUp();

		$defaults = array(
			'apiKey' => '',
			'defaultListId' => '',
		);
		$configs = (array)Configure::read('Mailchimp') + $defaults;
		$this->MailchimpSubscriberSource = new MailchimpSubscriberSource($configs);
	}

	public function testObject() {
		$this->assertTrue(is_object($this->MailchimpSubscriberSource));
		$this->assertIsA($this->MailchimpSubscriberSource, 'MailchimpSubscriberSource');
	}

	public function testRead() {
		$Model = ClassRegistry::init('Mailchimp.NewsletterSubscriber');
		$res = $this->MailchimpSubscriberSource->read($Model, array('conditions' => array('email' => 'mark@example.org')));
		$this->debug($res);
	}

	public function _testCreate() {
		$Model = ClassRegistry::init('Mailchimp.NewsletterSubscriber');
		$res = $this->MailchimpSubscriberSource->create($Model, array('conditions' => array('email' => 'mark@example.org')));
		//$this->assertTrue($res);
		$this->debug($res);
	}

	public function _testDelete() {
		$Model = ClassRegistry::init('Mailchimp.NewsletterSubscriber');
		$res = $this->MailchimpSubscriberSource->delete($Model, array('conditions' => array('email' => 'mark@example.org')));
		//$this->assertTrue($res);
		$this->debug($res);
	}

}