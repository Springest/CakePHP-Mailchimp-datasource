<?php

App::uses('MailchimpSubscriber', 'Mailchimp.Model');
App::uses('MyCakeTestCase', 'Tools.TestSuite');

class MailchimpSubscriberTest extends MyCakeTestCase {

	public $MailchimpSubscriber;

	public function setUp() {
		parent::setUp();
		$this->skipIf(!Configure::read('Mailchimp.apiKey'), 'No API key');

		$this->MailchimpSubscriber = new MailchimpSubscriber();
	}

	/**
	 * MailchimpSubscriberTest::testObject()
	 *
	 * @return void
	 */
	public function testObject() {
		$this->assertTrue(is_object($this->MailchimpSubscriber));
		$this->assertIsA($this->MailchimpSubscriber, 'MailchimpSubscriber');
	}

	/**
	 * MailchimpSubscriberTest::testBatchSubscribe()
	 *
	 * @return void
	 */
	public function testBatchSubscribe() {
		$emails = array('test@markscherer.de', 'another@markscherer.de');
		$options = array('doubleOptin' => false, 'updateExisting' => true);
		$res = $this->MailchimpSubscriber->batchSubscribe($emails, $options);
		$this->assertSame(2, $res['success_count']);
		$this->assertSame(0, $res['error_count']);
	}

	/**
	 * MailchimpSubscriberTest::testBatchUnubscribe()
	 *
	 * @return void
	 */
	public function testBatchUnubscribe() {
		$emails = array('test@markscherer.de', 'another@markscherer.de');
		$res = $this->MailchimpSubscriber->batchUnsubscribe($emails);
		$this->assertSame(2, $res['success_count']);
		$this->assertSame(0, $res['error_count']);
	}

	/**
	 * MailchimpSubscriberTest::testBasicSubscriptionInvalid()
	 *
	 * @return void
	 */
	public function testBasicSubscriptionInvalid() {
		$res = $this->MailchimpSubscriber->subscribe(array('email' => ''));
		$this->assertFalse($res);
		$this->assertEquals('ValidationError', $this->MailchimpSubscriber->response['name']);
	}

	/**
	 * MailchimpSubscriberTest::testBasicSubscriptionInvalid()
	 *
	 * @expectedException MailchimpException
	 * @return void
	 */
	public function testBasicSubscriptionInvalidException() {
		$this->MailchimpSubscriber->settings['exceptions'] = true;
		$this->MailchimpSubscriber->subscribe(array('email' => ''));
	}

	/**
	 * MailchimpSubscriberTest::testBasicSubscription()
	 *
	 * @return void
	 */
	public function testBasicSubscription() {
		$res = $this->MailchimpSubscriber->subscribe(array(
			'email' => 'kontakt@markscherer.de'),
		array(
			'doubleOptin' => false));
		$this->assertEquals('kontakt@markscherer.de', $res['email']);
	}

	/**
	 * MailchimpSubscriberTest::testBasicUnsubscription()
	 *
	 * @return void
	 */
	public function testBasicUnsubscription() {
		$res = $this->MailchimpSubscriber->unsubscribe(array('email' => 'kontakt@markscherer.de'));
		$this->assertSame(array('complete' => true), $res);
	}

	/**
	 * MailchimpSubscriberTest::testBasicUnsubscriptionInvalidException()
	 *
	 * @expectedException MailchimpException
	 * @return void
	 */
	public function testBasicUnsubscriptionInvalidException() {
		$this->MailchimpSubscriber->settings['exceptions'] = true;
		$this->MailchimpSubscriber->unsubscribe(array('email' => 'kontakt@xxx.de'));
	}

}