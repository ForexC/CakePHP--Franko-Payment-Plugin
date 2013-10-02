<?php
App::uses('PaymentAppController', 'Payment.Controller');

class FrankoAddressesController extends PaymentAppController {


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
		$this->FrankoAddress->recursive = 0;
		$frankoAddresses = $this->paginate();
		$this->set(compact('frankoAddresses'));
	}

	public function admin_view($id = null) {
		if (empty($id) || !($frankoAddress = $this->FrankoAddress->find('first', array('conditions'=>array('FrankoAddress.id'=>$id))))) {
			$this->Common->flashMessage(__('invalid record'), 'error');
			$this->Common->autoRedirect(array('action' => 'index'));
		}
		$this->set(compact('frankoAddress'));
	}

	public function admin_add() {
		if ($this->Common->isPosted()) {
			$this->FrankoAddress->create();
			if ($this->FrankoAddress->save($this->request->data)) {
				$var = $this->request->data['FrankoAddress']['address'];
				$this->Common->flashMessage(__('record add %s saved', h($var)), 'success');
				$this->Common->postRedirect(array('action' => 'index'));
			} else {
				$this->Common->flashMessage(__('formContainsErrors'), 'error');
			}
		}
		$users = $this->FrankoAddress->User->find('list');
		$this->set(compact('users'));
	}

	public function admin_edit($id = null) {
		if (empty($id) || !($frankoAddress = $this->FrankoAddress->find('first', array('conditions'=>array('FrankoAddress.id'=>$id))))) {
			$this->Common->flashMessage(__('invalid record'), 'error');
			$this->Common->autoRedirect(array('action' => 'index'));
		}
		if ($this->Common->isPosted()) {
			if ($this->FrankoAddress->save($this->request->data)) {
				$var = $this->request->data['FrankoAddress']['address'];
				$this->Common->flashMessage(__('record edit %s saved', h($var)), 'success');
				$this->Common->postRedirect(array('action' => 'index'));
			} else {
				$this->Common->flashMessage(__('formContainsErrors'), 'error');
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $frankoAddress;
		}
		$users = $this->FrankoAddress->User->find('list');
		$this->set(compact('users'));
	}

	public function admin_delete($id = null) {
		if (!$this->Common->isPosted()) {
			throw new MethodNotAllowedException();
		}
		if (empty($id) || !($frankoAddress = $this->FrankoAddress->find('first', array('conditions'=>array('FrankoAddress.id'=>$id), 'fields'=>array('id', 'address'))))) {
			$this->Common->flashMessage(__('invalid record'), 'error');
			$this->Common->autoRedirect(array('action'=>'index'));
		}
		$var = $frankoAddress['FrankoAddress']['address'];

		if ($this->FrankoAddress->delete($id)) {
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
