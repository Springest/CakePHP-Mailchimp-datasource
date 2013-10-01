<?php
/**
 * Group test - Mailchimp
 */
class AllDatasourceTestsTest extends PHPUnit_Framework_TestSuite {

	/**
	 * Suite method, defines tests for this suite.
	 *
	 * @return void
	 */
	public static function suite() {
		$Suite = new CakeTestSuite('All Datasource tests');
		$path = dirname(__FILE__);
		$Suite->addTestDirectory($path . DS . 'Model' . DS . 'Datasource');
		return $Suite;
	}
}
