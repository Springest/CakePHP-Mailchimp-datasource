<?php

App::uses('MailchimpAppModel', 'Mailchimp.Model');
App::uses('MyCakeTestCase', 'Tools.TestSuite');

class MailchimpAppModelTest extends MyCakeTestCase {

	public $MailchimpAppModel;

	public function setUp() {
		parent::setUp();

		$this->MailchimpAppModel = new MailchimpAppModel();
	}

	public function testObject() {
		$this->assertTrue(is_object($this->MailchimpAppModel));
		$this->assertIsA($this->MailchimpAppModel, 'MailchimpAppModel');
	}

}