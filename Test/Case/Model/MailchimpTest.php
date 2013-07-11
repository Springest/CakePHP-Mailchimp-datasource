<?php

App::uses('Mailchimp', 'Mailchimp.Model');
App::uses('MyCakeTestCase', 'Tools.TestSuite');

class MailchimpTest extends MyCakeTestCase {

	public $Mailchimp;

	public function setUp() {
		parent::setUp();
		$this->Mailchimp = new Mailchimp();
	}

	public function testObject() {
		$this->assertTrue(is_object($this->Mailchimp));
		$this->assertIsA($this->Mailchimp, 'Mailchimp');
	}

	/**
	 * MailchimpTest::testPing()
	 *
	 * @return void
	 */
	public function testPing() {
		$res = $this->Mailchimp->ping();
		$this->debug($res);
		$this->assertEquals('Everything\'s Chimpy!', $res);
	}

	/**
	 * MailchimpTest::testApiKeys()
	 *
	 * @return void
	 */
	public function testApiKeys() {
		$res = $this->Mailchimp->apikeys(null, null);
		$this->debug($res);
		$this->assertFalse($res);
	}

}