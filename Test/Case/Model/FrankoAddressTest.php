<?php
/* FrankoAddress Test cases generated on: 2011-07-16 02:50:12 : 1310777412*/
App::uses('FrankoAddress', 'Payment.Model');
App::uses('MyCakeTestCase', 'Tools.TestSuite');

class FrankoAddressTest extends MyCakeTestCase {
	public function setUp() {
		$this->FrankoAddress = ClassRegistry::init('FrankoAddress');
	}

	public function tearDown() {
		unset($this->FrankoAddress);
		ClassRegistry::flush();
	}

}
