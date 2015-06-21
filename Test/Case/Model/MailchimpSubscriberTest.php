<?php

App::uses('MailchimpSubscriber', 'Mailchimp.Model');
App::uses('MyCakeTestCase', 'Tools.TestSuite');

class MailchimpSubscriberTest extends MyCakeTestCase {

	public $MailchimpSubscriber;

	public $time;

	public function setUp() {
		parent::setUp();
		if ($this->isDebug()) {
			$this->skipIf(!Configure::read('Mailchimp.apiKey'), 'No API key');
		} else {
			Configure::write('Mailchimp.apiKey', 'foo-bar');
		}

		$this->MailchimpSubscriber = new MailchimpSubscriber();

		if (!$this->isDebug()) {
			$this->MailchimpSubscriber->Mailchimp = $this->getMock('MailchimpLib', array('_get'));
			$this->mockPath = CakePlugin::path('Mailchimp') . 'Test' . DS . 'test_files' . DS . 'mailchimp' . DS;
			return;
		}

		if (!Configure::read('Test.time')) {
			Configure::write('Test.time', time());
		}
		$this->time = Configure::read('Test.time');
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
		if (!$this->isDebug()) {
			$this->MailchimpSubscriber->Mailchimp->expects($this->once())
			->method('_get')
			->will($this->returnValue(file_get_contents($this->mockPath . 'lists_batch_subscribe.json')));
		}

		$emails = array('test' . $this->time . '@myexample.org', 'another' . $this->time . '@myexample.org');
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
		if ($this->isDebug()) {
			sleep(5);
		} else {
			$this->MailchimpSubscriber->Mailchimp->expects($this->once())
			->method('_get')
			->will($this->returnValue(file_get_contents($this->mockPath . 'lists_batch_unsubscribe.json')));
		}

		$emails = array('test' . $this->time . '@myexample.org', 'another' . $this->time . '@myexample.org');
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
		if (!$this->isDebug()) {
			$this->MailchimpSubscriber->Mailchimp->expects($this->once())
			->method('_get')
			->will($this->returnValue(file_get_contents($this->mockPath . 'lists_subscribe_error.json')));
		}

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
		if (!$this->isDebug()) {
			$this->MailchimpSubscriber->Mailchimp->expects($this->once())
			->method('_get')
			->will($this->returnValue(file_get_contents($this->mockPath . 'lists_subscribe_error.json')));
		}

		$this->MailchimpSubscriber->settings['exceptions'] = true;
		$this->MailchimpSubscriber->subscribe(array('email' => ''));
	}

	/**
	 * MailchimpSubscriberTest::testBasicSubscription()
	 *
	 * @return void
	 */
	public function testBasicSubscription() {
		if (!$this->isDebug()) {
			$this->MailchimpSubscriber->Mailchimp->expects($this->once())
			->method('_get')
			->will($this->returnValue(file_get_contents($this->mockPath . 'lists_subscribe.json')));
		}

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
		if (!$this->isDebug()) {
			$this->MailchimpSubscriber->Mailchimp->expects($this->once())
			->method('_get')
			->will($this->returnValue(file_get_contents($this->mockPath . 'lists_unsubscribe.json')));
		}

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
		if (!$this->isDebug()) {
			$this->MailchimpSubscriber->Mailchimp->expects($this->once())
			->method('_get')
			->will($this->returnValue(file_get_contents($this->mockPath . 'lists_unsubscribe_error.json')));
		}

		$this->MailchimpSubscriber->settings['exceptions'] = true;
		$this->MailchimpSubscriber->unsubscribe(array('email' => 'kontakt@xxx.de'));
	}

}