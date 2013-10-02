<?php
/* FrankoAddresses Test cases generated on: 2011-07-16 02:50:23 : 1310777423*/
App::uses('FrankoAddressesController', 'Payment.Controller');
App::uses('MyCakeTestCase', 'Tools.TestSuite');

class TestFrankoAddressesController extends FrankoAddressesController {
	public $autoRender = false;

	public function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}
}

class FrankoAddressesControllerTest extends MyCakeTestCase {
	public function setUp() {
		$this->FrankoAddresses = new TestFrankoAddressesController();
		$this->FrankoAddresses->constructClasses();
	}

	public function tearDown() {
		unset($this->FrankoAddresses);
		ClassRegistry::flush();
	}

}
