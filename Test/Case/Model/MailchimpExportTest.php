<?php

App::uses('MailchimpExport', 'Mailchimp.Model');
App::uses('MyCakeTestCase', 'Tools.TestSuite');

class MailchimpExportTest extends MyCakeTestCase {

	public $MailchimpExport;

	public function setUp() {
		parent::setUp();
		if ($this->isDebug()) {
			$this->skipIf(!Configure::read('Mailchimp.apiKey'), 'No API key');
		} else {
			Configure::write('Mailchimp.apiKey', 'foo-bar');
		}

		$this->MailchimpExport = new MailchimpExport();

		if (!$this->isDebug()) {
			$this->MailchimpExport = $this->getMock('MailchimpExport', ['_get']);
			$this->mockPath = CakePlugin::path('Mailchimp') . 'Test' . DS . 'test_files' . DS . 'mailchimp' . DS;
		}
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
	public function testExportActivityError() {
		if (!$this->isDebug()) {
			$this->MailchimpExport->expects($this->once())
			->method('_get')
			->will($this->returnValue(file_get_contents($this->mockPath . 'export_campaignSubscriberActivity.json')));
		}

		$this->MailchimpExport->exportActivity();
	}

	/**
	 * MailchimpExportTest::testExportMembers()
	 *
	 * @return void
	 */
	public function testExportMembers() {
		if (!$this->isDebug()) {
			$this->MailchimpExport->expects($this->once())
			->method('_get')
			->will($this->returnValue(file_get_contents($this->mockPath . 'export_list.json')));
		}

		$res = $this->MailchimpExport->exportMembers();
		$this->assertTrue(!empty($res) && is_array($res));
	}

}