<?php

App::uses('MailchimpExport', 'Mailchimp.Model');
App::uses('MyCakeTestCase', 'Tools.TestSuite');

class MailchimpExportTest extends MyCakeTestCase {

	public $MailchimpExport;

	public function setUp() {
		parent::setUp();
		$this->MailchimpExport = new MailchimpExport();
	}

	public function testObject() {
		$this->assertTrue(is_object($this->MailchimpExport));
		$this->assertIsA($this->MailchimpExport, 'MailchimpExport');
	}

	/**
	 * MailchimpExportTest::testExportActivity()
	 *
	 * @expectedException MailchimpException
	 * @return void
	 */
	public function testExportActivity() {
		$this->MailchimpExport->exportActivity();
	}

	/**
	 * MailchimpExportTest::testExportMembers()
	 *
	 * @return void
	 */
	public function testExportMembers() {
		$res = $this->MailchimpExport->exportMembers();
		$this->debug($res);
		//$this->assertTrue(!empty($res) && is_array($res));
	}

}