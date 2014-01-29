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
		$this->assertEquals(array('msg' => 'Everything\'s Chimpy!'), $res);
	}

	/**
	 * MailchimpTest::testInlineCss()
	 *
	 * @return void
	 */
	public function testInlineCss() {
		$html = <<<HTML
<style>
div.x {
	font-weight: bold;
}
</style>
<h1>Header</h1>
<div class="x">Some bold text</div>
End of block.
HTML;
		$res = $this->Mailchimp->inlineCss($html);
		$expected = array(
			'html' => '
<h1>Header</h1>
<div class="x" style="font-weight: bold;">Some bold text</div>
End of block.'
		);
		$this->assertEquals($expected, $res);
	}

	/**
	 * MailchimpTest::testInlineCss()
	 *
	 * @return void
	 */
	public function testGenerateText() {
		$html = <<<HTML
<h1>Header</h1>
<div class="x">Some bold text</div>
End of block.
HTML;
		$res = $this->Mailchimp->generateText('html', array('html' => $html));
		$expected = <<<TEXT
** Header
------------------------------------------------------------

Some bold text

End of block.
TEXT;
		$this->assertTextEquals($expected, trim($res['text']));
	}

}