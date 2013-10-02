<?php
App::uses('AppShell', 'Console/Command');

/**
 * 2011-07-20 ms
 */
class FrankoShell extends AppShell {

	public $uses = array();
	public $tasks = array('InstantPaymentNotification');


	/**
	 * Overwrite shell initialize to dynamically load
	 */
	public function initialize() {
		parent::initialize();

	}


	/**
	 * Output some basic usage Info.
	 */
	public function help() {
		$this->out('CakePHP Franko Shell:');
		$this->hr();
		$this->out('Usage:');
		$this->out('	cake franko help');
		$this->out('		-> Display this Help message');
		$this->out('	cake franko enable');
		$this->out('		-> adds tasks to queue (if available)');
		$this->out('	cake franko run');
		$this->out('		-> run all tasks');
		$this->out('Notes:');
		$this->out('	Queue Plugin required for "enable"');
		$this->out('Available Tasks:');
		foreach ($this->taskNames as $loadedTask) {
			$this->out('	->' . $loadedTask);
		}
	}


	/**
	 * 2011-07-20 ms
	 */
	public function enable() {
		if (count($this->args) < 1) {
			$this->out('Please call like this:');
			$this->out('       cake queue add <taskname>');
		} else {


		}
	}

	/**
	 * Run a loop.
	 * 2011-07-20 ms
	 */
	public function run() {
		foreach ($this->tasks as $task) {
			$this->{$task}->execute();
		}

		$this->out('Done');
	}


}
