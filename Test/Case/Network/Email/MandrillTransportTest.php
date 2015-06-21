<?php
App::uses('CakeEmail', 'Network/Email');
App::uses('MandrillTransport', 'Mailchimp.Network/Email');
App::uses('HttpSocketResponse', 'Network/Http');

class MandrillTransportTest extends CakeTestCase {

	public $MandrillTransport;

	/**
	 * Setup
	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();
		if (!class_exists('MockSocket')) {
			$this->getMock('CakeSocket', array('read', 'write', 'connect', 'enableCrypto'), array(), 'MockSocket');
		}
		$this->socket = new MockSocket();

		$this->MandrillTransport = new MandrillTestTransport();
		//$this->MandrillTransport->config(array('url' => 'https://mandrillapp.com/api/1.0/'));
	}

	/**
	 * testConnectEhlo method
	 *
	 * @expectedException InternalErrorException
	 * @return void
	 */
	public function testSendWithoutKey() {
		Configure::delete('Mandrill.apiKey');

		$email = new TestCakeEmail();
		$this->MandrillTransport->send($email);
	}

	/**
	 * testConnectEhlo method
	 *
	 * @return void
	 */
	public function testSend() {
		$this->skipIf(!Configure::read('Mandrill.apiKey'));

		$Mock = $this->getMock('MandrillTestTransport', array('_post'));
		$Mock->expects($this->once())
			->method('_post')
			->will($this->returnValue(new HttpSocketResponse()));
		$this->MandrillTransport = $Mock;

		$email = new TestCakeEmail();
		$email->from('dummy@dummy.de');
		$email->to('dummy@dummy.de');
		$email->subject('Test');
		$email->emailFormat('both');
		//$email->template('mandrill', 'mandrill');
		//$email->viewVars(array('test' => 'foo'));
		$email->setContent('some <b>html</b>.', 'some plain **text** with markup.');
		$res = $this->MandrillTransport->send($email);
		debug($res);
		$this->assertEquals('some <b>html</b>.', $res['message']['message']['html']);
		$this->assertEquals('some plain **text** with markup.', $res['message']['message']['text']);
	}

	/**
	 * MandrillTransportTest::testSendReal()
	 *
	 * @return void
	 */
	public function _testSendReal() {
		$this->skipIf(!Configure::read('Mandrill.apiKey'));

		//$this->socket = new CakeSocket();

		$email = new TestCakeEmail();
		$email->from('dummy@dummy.de');
		$email->to('dummy@dummy.de');
		$email->subject('Test');
		$email->emailFormat('both');
		$email->setContent('some <b>html</b>.', 'some plain **text** with markup.');
		$res = $this->MandrillTransport->send($email);
		debug($res);
		$this->assertEquals('some <b>html</b>.', $res['message']['message']['html']);
		$this->assertEquals('some plain **text** with markup.', $res['message']['message']['text']);
	}

	/**
	 * testEmptyConfigArray method
	 *
	 * @return void
	 */
	public function testEmptyConfigArray() {
		$expected = $this->MandrillTransport->config(array(
			'client' => 'myhost.com',
			'port' => 666
		));

		$this->assertEquals(666, $expected['port']);

		$result = $this->MandrillTransport->config(array());
		$this->assertEquals($expected, $result);
	}

}

/**
 * Help to test MandrillTransport
 *
 */
class TestCakeEmail extends CakeEmail {

	public function setContent($html, $text) {
		$this->_htmlMessage = $html;
		$this->_textMessage = $text;
	}

}

/**
 * Help to test MandrillTransport
 *
 */
class MandrillTestTransport extends MandrillTransport {

	/**
	 * Helper to change the CakeEmail
	 *
	 * @param object $cakeEmail
	 * @return void
	 */
	public function setCakeEmail($cakeEmail) {
		$this->_cakeEmail = $cakeEmail;
	}

	/**
	 * Disabled the socket change
	 *
	 * @return void
	 */
	protected function _generateSocket() {
	}

	/**
	 * Magic function to call protected methods
	 *
	 * @param string $method
	 * @param string $args
	 * @return mixed
	 */
	public function __call($method, $args) {
		$method = '_' . $method;
		return $this->$method();
	}

}
