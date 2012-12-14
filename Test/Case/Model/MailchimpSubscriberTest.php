<?php

App::uses('MailchimpSubscriber', 'Mailchimp.Model');
App::uses('MyCakeTestCase', 'Tools.Lib');

class MailchimpSubscriberTest extends MyCakeTestCase {

	public $MailchimpSubscriber;

	public function startTest() {
		$this->MailchimpSubscriber = new MailchimpSubscriber();
	}

	public function testObject() {
		$this->assertTrue(is_object($this->MailchimpSubscriber));
		$this->assertIsA($this->MailchimpSubscriber, 'MailchimpSubscriber');
	}

	public function testBasicSubscription() {
		$res = $this->MailchimpSubscriber->subscribe(array('email'=>'kontakt@markscherer.de'));
		die(returns($res));
	}
	
	public function testBasicUnsubscription() {
		$res = $this->MailchimpSubscriber->unsubscribe(array('email'=>'kontakt@markscherer.de'));
		die(returns($res));
	}
	
}