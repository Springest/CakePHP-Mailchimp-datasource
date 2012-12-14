<?php
/**
 * group test - Mailchimp
 */
class AllDatasourceTestsTest extends PHPUnit_Framework_TestSuite {

	/**
	 * suite method, defines tests for this suite.
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
