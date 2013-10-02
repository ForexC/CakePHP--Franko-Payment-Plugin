<?php

App::uses('FrankoHelper', 'Payment.View/Helper');
App::uses('MyCakeTestCase', 'Tools.TestSuite');
App::uses('View', 'View');
App::uses('Controller', 'Controller');

/**
 * Franko Test Case
 */
class FrankoTest extends MyCakeTestCase {

	public $Franko;

	/**
	 * setUp method
	 *
	 * @access public
	 * @return void
	 */
	public function setUp() {
		$this->Franko = new FrankoHelper(new View(new Controller(new CakeRequest(null, false), null)));
	}

	/**
	 * test image
	 *
	 * 2011-07-20 ms
	 */
	public function testImage() {
		$res = $this->Franko->image(null, array('title' => 'XYZ'));
		pr($res);

		$res = $this->Franko->image(24);
		pr($res);

		$res = $this->Franko->image(32);
		pr($res);

		$res = $this->Franko->image(48, array('onclick'=>'alert(\'HI\')', 'title' => 'XYZ'));
		pr($res);

		$res = $this->Franko->image(64, array('title' => 'XYZ'));
		pr($res);
	}


	public function testBox() {
		$res = $this->Franko->paymentBox(3.123456, '4578345734895734895734df34873847283478');
		pr($res);

		$res = $this->Franko->paymentBox(4, '');
		pr($res);

		$res = $this->Franko->donationBox('4578345734895734895734df34873847283478');
		pr($res);
	}




	/**
	 * tearDown method
	 *
	 * @access public
	 * @return void
	 */
	public function tearDown() {
		//unset($this->Franko);
	}

}
