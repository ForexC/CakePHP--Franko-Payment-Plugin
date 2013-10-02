<?php
//App::uses('AppController', 'Controller');
App::uses('PaymentAppController', 'Payment.Controller');

class FrankoTransactionsController extends PaymentAppController {


	public $paginate = array();

	public function beforeFilter() {
		parent::beforeFilter();
	}



/****************************************************************************************
 * USER functions
 ****************************************************************************************/


/****************************************************************************************
 * ADMIN functions
 ****************************************************************************************/

	public function admin_index() {
		$this->FrankoTransaction->recursive = 0;
		$frankoTransactions = $this->paginate();
		$this->set(compact('frankoTransactions'));
	}

	public function admin_view($id = null) {
		if (empty($id) || !($frankoTransaction = $this->FrankoTransaction->find('first', array('conditions'=>array('FrankoTransaction.id'=>$id))))) {
			$this->Common->flashMessage(__('invalid record'), 'error');
			$this->Common->autoRedirect(array('action' => 'index'));
		}
		$this->set(compact('frankoTransaction'));
	}

	public function admin_add() {
		if ($this->Common->isPosted()) {
			$this->FrankoTransaction->create();
			if ($this->FrankoTransaction->save($this->request->data)) {
				$var = $this->request->data['FrankoTransaction']['amount'];
				$this->Common->flashMessage(__('record add %s saved', h($var)), 'success');
				$this->Common->postRedirect(array('action' => 'index'));
			} else {
				$this->Common->flashMessage(__('formContainsErrors'), 'error');
			}
		}
		$addresses = $this->FrankoTransaction->Address->find('list');
		$this->set(compact('addresses'));
	}

	public function admin_edit($id = null) {
		if (empty($id) || !($frankoTransaction = $this->FrankoTransaction->find('first', array('conditions'=>array('FrankoTransaction.id'=>$id))))) {
			$this->Common->flashMessage(__('invalid record'), 'error');
			$this->Common->autoRedirect(array('action' => 'index'));
		}
		if ($this->Common->isPosted()) {
			if ($this->FrankoTransaction->save($this->request->data)) {
				$var = $this->request->data['FrankoTransaction']['amount'];
				$this->Common->flashMessage(__('record edit %s saved', h($var)), 'success');
				$this->Common->postRedirect(array('action' => 'index'));
			} else {
				$this->Common->flashMessage(__('formContainsErrors'), 'error');
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $frankoTransaction;
		}
		$addresses = $this->FrankoTransaction->Address->find('list');
		$this->set(compact('addresses'));
	}

	public function admin_delete($id = null) {
		if (!$this->Common->isPosted()) {
			throw new MethodNotAllowedException();
		}
		if (empty($id) || !($frankoTransaction = $this->FrankoTransaction->find('first', array('conditions'=>array('FrankoTransaction.id'=>$id), 'fields'=>array('id', 'amount'))))) {
			$this->Common->flashMessage(__('invalid record'), 'error');
			$this->Common->autoRedirect(array('action'=>'index'));
		}
		$var = $frankoTransaction['FrankoTransaction']['amount'];

		if ($this->FrankoTransaction->delete($id)) {
			$this->Common->flashMessage(__('record del %s done', h($var)), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Common->flashMessage(__('record del %s not done exception', h($var)), 'error');
		$this->Common->autoRedirect(array('action' => 'index'));
	}



/****************************************************************************************
 * protected/interal functions
 ****************************************************************************************/


/****************************************************************************************
 * deprecated/test functions
 ****************************************************************************************/

}
