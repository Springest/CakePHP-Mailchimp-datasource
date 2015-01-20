<?php

App::uses('MailchimpCampaign', 'Mailchimp.Model');
App::uses('MyCakeTestCase', 'Tools.TestSuite');

class MailchimpCampaignTest extends MyCakeTestCase {

	public $MailchimpCampaign;

	public function setUp() {
		parent::setUp();
		if ($this->isDebug()) {
			$this->skipIf(!Configure::read('Mailchimp.apiKey'), 'No API key');
		} else {
			Configure::write('Mailchimp.apiKey', 'foo-bar');
		}

		$this->MailchimpCampaign = new MailchimpCampaign();

		if (!$this->isDebug()) {
			$this->MailchimpCampaign->Mailchimp = $this->getMock('MailchimpLib', array('_get'));
			$this->mockPath = CakePlugin::path('Mailchimp') . 'Test' . DS . 'test_files' . DS . 'mailchimp' . DS;
		}
	}

	/**
	 * MailchimpCampaignTest::testObject()
	 *
	 * @return void
	 */
	public function testObject() {
		$this->assertTrue(is_object($this->MailchimpCampaign));
		$this->assertIsA($this->MailchimpCampaign, 'MailchimpCampaign');
	}

	/**
	 * MailchimpCampaignTest::testCampaigns()
	 *
	 * @return void
	 */
	public function testCampaigns() {
		if (!$this->isDebug()) {
			$this->MailchimpCampaign->Mailchimp->expects($this->once())
			->method('_get')
			->will($this->returnValue(file_get_contents($this->mockPath . 'campaigns_list.json')));
		}

		$res = $this->MailchimpCampaign->campaigns();
		$this->debug($res);
		$this->assertTrue(is_int($res['total']));
		$this->assertTrue(isset($res['data']));
		if ($res['data']) {
			$this->assertTrue(!empty($res['data'][0]['tracking']));
		}
	}

	/**
	 * MailchimpCampaignTest::testCampaignSendTest()
	 *
	 * @return void
	 */
	public function testCampaignSendTest() {
		if (!$this->isDebug()) {
			$this->MailchimpCampaign->Mailchimp->expects($this->once())
			->method('_get')
			->will($this->returnValue(file_get_contents($this->mockPath . 'campaigns_send_test_error.json')));
		}

		$res = $this->MailchimpCampaign->campaignSendTest(array('kontakt@markscherer.de'));
		$this->assertFalse($res);
	}

}