<?php

App::uses('MailchimpSubscriberSource', 'Mailchimp.Model/Datasource');
App::uses('MyCakeTestCase', 'Tools.Lib');

class MailchimpSubscriberSourceTest extends MyCakeTestCase {

	public $MailchimpSubscriberSource;

	public $fixtures = array('plugin.Mailchimp.NewsletterSubscriber');

	public function startTest() {
		$c = array(
			'apiKey' => '',
			'defaultListId' => '',
			'baseUrl' => ''
		);
		$res = (array) Configure::read('Mailchimp');
		$c = am($c, $res);
		
		$this->MailchimpSubscriberSource = new MailchimpSubscriberSource($c);
	}

	public function testObject() {
		$this->assertTrue(is_object($this->MailchimpSubscriberSource));
		$this->assertIsA($this->MailchimpSubscriberSource, 'MailchimpSubscriberSource');
	}

	public function testRead() {
		$Model = ClassRegistry::init('Mailchimp.NewsletterSubscriber');
		$res = $this->MailchimpSubscriberSource->read($Model, array('conditions'=>array('email'=>'markscherer@gmx.de')));
		
		//die(returns($res));
		return $res;
	}

	public function testBasicSubscription() {
		$Model = ClassRegistry::init('Mailchimp.NewsletterSubscriber');
		$res = $this->MailchimpSubscriberSource->subscribe($Model, array('conditions'=>array('email'=>'kontakt@markscherer.de')));
		//$this->assertTrue($res);
	}
	
	public function testBasicUnsubscription() {
		$Model = ClassRegistry::init('Mailchimp.NewsletterSubscriber');
		$res = $this->MailchimpSubscriberSource->unsubscribe($Model, array('conditions'=>array('email'=>'kontakt@markscherer.de')));
		//$this->assertTrue($res);
	}
	
}