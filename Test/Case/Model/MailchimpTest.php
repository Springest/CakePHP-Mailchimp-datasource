<?php

App::uses('Mailchimp', 'Mailchimp.Model');
App::uses('MyCakeTestCase', 'Tools.TestSuite');

class MailchimpTest extends MyCakeTestCase {

	public $Mailchimp;

	public function setUp() {
		parent::setUp();

		if ($this->isDebug()) {
			$this->skipIf(!Configure::read('Mailchimp.apiKey'), 'No API key');
		} else {
			Configure::write('Mailchimp.apiKey', 'foo-bar');
		}

		$this->Mailchimp = new Mailchimp();

		if (!$this->isDebug()) {
			$this->Mailchimp->Mailchimp = $this->getMock('MailchimpLib', array('_get'));
			$this->mockPath = CakePlugin::path('Mailchimp') . 'Test' . DS . 'test_files' . DS . 'mailchimp' . DS;
		}
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
		if (!$this->isDebug()) {
			$this->Mailchimp->Mailchimp->expects($this->once())
			->method('_get')
			->will($this->returnValue(file_get_contents($this->mockPath . 'helper_ping.json')));
		}

		$res = $this->Mailchimp->ping();
		$this->assertEquals(array('msg' => 'Everything\'s Chimpy!'), $res);
	}

	/**
	 * MailchimpTest::testInlineCss()
	 *
	 * @return void
	 */
	public function testInlineCss() {
		if (!$this->isDebug()) {
			$this->Mailchimp->Mailchimp->expects($this->once())
			->method('_get')
			->will($this->returnValue(file_get_contents($this->mockPath . 'helper_inline_css.json')));
		}

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
		$this->assertTextEquals($expected, $res);
	}

	/**
	 * MailchimpTest::testInlineCss()
	 *
	 * @return void
	 */
	public function testGenerateText() {
		if (!$this->isDebug()) {
			$this->Mailchimp->Mailchimp->expects($this->once())
			->method('_get')
			->will($this->returnValue(file_get_contents($this->mockPath . 'helper_generate_text.json')));
		}

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