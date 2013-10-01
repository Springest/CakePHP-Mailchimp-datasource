<?php
/**
 * Group test - Mailchimp
 */
class AllMailchimpTestsTest extends PHPUnit_Framework_TestSuite {

	/**
	 * Suite method, defines tests for this suite.
	 *
	 * @return void
	 */
	public static function suite() {
		$Suite = new CakeTestSuite('All Mailchimp tests');
		$path = dirname(__FILE__);
		$Suite->addTestDirectory($path . DS . 'Model');
		$Suite->addTestDirectory($path . DS . 'Model' . DS . 'Datasource');
		return $Suite;
	}

}
