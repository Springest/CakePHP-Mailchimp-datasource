<?php

App::uses('MailchimpSubscriber', 'Mailchimp.Model');
App::uses('MyCakeTestCase', 'Tools.TestSuite');

class MailchimpSubscriberTest extends MyCakeTestCase {

	public $MailchimpSubscriber;

	public function setUp() {
		parent::setUp();
		$this->MailchimpSubscriber = new MailchimpSubscriber();
	}

	public function testObject() {
		$this->assertTrue(is_object($this->MailchimpSubscriber));
		$this->assertIsA($this->MailchimpSubscriber, 'MailchimpSubscriber');
	}

	public function testBasicSubscription() {
		$res = $this->MailchimpSubscriber->subscribe(array('email' => 'kontakt@markscherer.de'));
		$this->debug($res);
	}

	public function testBasicUnsubscription() {
		$res = $this->MailchimpSubscriber->unsubscribe(array('email' => 'kontakt@markscherer.de'));
		$this->debug($res);
	}

}