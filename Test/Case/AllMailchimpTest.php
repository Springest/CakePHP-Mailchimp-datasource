<?php
/**
 * Group test - Mailchimp
 */
class AllMailchimpTest extends PHPUnit_Framework_TestSuite {

	/**
	 * Suite method, defines tests for this suite.
	 *
	 * @return void
	 */
	public static function suite() {
		$Suite = new CakeTestSuite('All Mailchimp tests');
		$path = dirname(__FILE__);
		$Suite->addTestDirectoryRecursive($path . DS . 'Model');
		$Suite->addTestDirectoryRecursive($path . DS . 'Lib');
		$Suite->addTestDirectoryRecursive($path . DS . 'Network');
		return $Suite;
	}

}
