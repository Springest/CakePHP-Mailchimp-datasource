<?php

App::uses('MailchimpSubscriber', 'Mailchimp.Model');
App::uses('MyCakeTestCase', 'Tools.TestSuite');

class MailchimpSubscriberTest extends MyCakeTestCase {

	public $MailchimpSubscriber;

	public $time;

	public function setUp() {
		parent::setUp();
		$this->skipIf(!Configure::read('Mailchimp.apiKey'), 'No API key');

		$this->MailchimpSubscriber = new MailchimpSubscriber();

		if (!isset($this->time)) {
			$this->time = time();
		}
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
		$emails = array('test' . $this->time . '@myexample.org', 'another' . $this->time . '@myexample.org');
		$options = array('doubleOptin' => false, 'updateExisting' => true);
		$res = $this->MailchimpSubscriber->batchSubscribe($emails, $options);
		debug($res);
		$this->assertSame(2, $res['success_count']);
		$this->assertSame(0, $res['error_count']);
	}

	/**
	 * MailchimpSubscriberTest::testBatchUnubscribe()
	 *
	 * @return void
	 */
	public function testBatchUnubscribe() {
		sleep(5);
		$emails = array('test' . $this->time . '@myexample.org', 'another' . $this->time . '@myexample.org');
		$res = $this->MailchimpSubscriber->batchUnsubscribe($emails);
		debug($res);
		$this->assertSame(2, $res['success_count']);
		$this->assertSame(0, $res['error_count']);
	}

	/**
	 * MailchimpSubscriberTest::testBasicSubscriptionInvalid()
	 *
	 * @return void
	 */
	public function testBasicSubscriptionInvalid() {
		$args = array(
			'fii' => 'bar'
		);

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
			'email' => 'kontakt@myexample.org'),
		array(
			'doubleOptin' => false));
		$this->assertEquals('kontakt@myexample.org', $res['email']);
	}

	/**
	 * MailchimpSubscriberTest::testBasicUnsubscription()
	 *
	 * @return void
	 */
	public function testBasicUnsubscription() {
		$res = $this->MailchimpSubscriber->unsubscribe(array('email' => 'kontakt@myexample.org'));
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