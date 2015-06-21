<?php

App::uses('MandrillLib', 'Mailchimp.Lib');
App::uses('MyCakeTestCase', 'Tools.TestSuite');

class MandrillLibTest extends MyCakeTestCase {

	public $Mandrill;

	public function setUp() {
		parent::setUp();

		$this->skipIf(!Configure::read('Mandrill.apiKey'), 'No apiKey found');

		$this->Mandrill = new MandrillLib();
	}

	/**
	 * MandrillLibTest::testSendersList()
	 *
	 * @return void
	 */
	public function testSendersList() {
		$senders = $this->Mandrill->call('senders/list');
		$this->assertTrue(is_array($senders));
	}

}
