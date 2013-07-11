<?php

App::uses('MailchimpCampaign', 'Mailchimp.Model');
App::uses('MyCakeTestCase', 'Tools.TestSuite');

class MailchimpCampaignTest extends MyCakeTestCase {

	public $MailchimpCampaign;

	public function setUp() {
		parent::setUp();
		$this->MailchimpCampaign = new MailchimpCampaign();
	}

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
		$res = $this->MailchimpCampaign->campaignSendTest(array('markscherer@gmx.de'));
		debug($res);
		debug($this->MailchimpCampaign->Mailchimp->errorCode);
		debug($this->MailchimpCampaign->Mailchimp->errorMessage);
	}

}