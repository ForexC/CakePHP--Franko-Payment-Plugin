<?php
App::uses('PaymentAppController', 'Payment.Controller');

class FrankoController extends PaymentAppController {


	public $helpers = array('Tools.Numeric');
	public $uses = array('Payment.FrankoTransaction');

	public function beforeFilter() {
		parent::beforeFilter();

		# temporary
		if (isset($this->Auth)) {
			//$this->Auth->allow();
		}
	}



/****************************************************************************************
 * ADMIN functions
 ****************************************************************************************/

	/**
	 * franko admincenter (main overview)
	 * 2011-07-26 ms
	 */
	public function admin_index() {
		$details = $infos = array();
		try {
			if (Configure::read('Franko.username') && Configure::read('Franko.password')) {
				$details = array(
					'accounts' => $this->FrankoTransaction->Franko->listAccounts(),
					//'account' => $this->FrankoTransaction->Franko->getAccountAddress(),
					'active' => Configure::read('Franko.account'),
					'addresses' => $this->FrankoTransaction->Franko->getAddressesByAccount(),
				);
				$infos = $this->FrankoTransaction->Franko->getInfo();
			} else {
				$this->Common->flashMessage('Zugangsdaten fehlen. Kann keine Verbindung aufbauen.', 'warning');
			}
		} catch (FrankoClientException $e) {
			$this->Common->flashMessage($e->getMessage(), 'error');
		}

		if ($this->Common->isPosted()) {
			try {
				$this->FrankoTransaction->set($this->request->data);
				if ($this->FrankoTransaction->validates()) {
					$addressDetails = array();
					if (Configure::read('Franko.username') && Configure::read('Franko.password')) {
						$addressDetails['firstSeen'] = $this->FrankoTransaction->Franko->addressFirstSeen($this->request->data['FrankoAddress']['address']);

					}
					$this->Common->flashMessage('Valid', 'success');
				} else {
					$this->Common->flashMessage('Invalid', 'error');
				}
			} catch (FrankoClientException $e) {
				$this->Common->flashMessage($e->getMessage(), 'error');
			}
		}


		$this->set(compact('infos', 'details'));
	}

	public function admin_address_details($address = null) {
		if (empty($address) || !($this->FrankoTransaction->set(array('address'=>$address)) && $this->FrankoTransaction->validates())) {
			$this->Common->autoRedirect(array('action'=>'index'));
		}

		//TODO
	}

	public function admin_transfer() {
		$accounts = $this->FrankoTransaction->Franko->listAccounts();
		$addresses = $this->FrankoTransaction->Franko->getAddressesByAccount($this->FrankoTransaction->ownAccount());
		$ownAddresses = $this->FrankoTransaction->addressList($addresses);
		$ownAccounts = $this->FrankoTransaction->accountList($accounts);

		if (!empty($this->request->data) && isset($this->request->data['Franko']['own_account_id'])) {
			$this->request->data['FrankoTransaction']['from_account'] = $this->request->data['Franko']['own_account_id'];
			$this->FrankoTransaction->set($this->request->data);
			if ($this->FrankoTransaction->validates()) {
				$this->Session->write('Franko.account', $this->request->data['Franko']['own_account_id']);
				$this->Common->flashMessage('Changed', 'success');
				$this->redirect(array('action'=>'transfer'));
			} else {
				$this->FrankoTransaction->validationErrors = array();
				$this->Common->flashMessage('formContainsErrors', 'error');
			}

		} elseif (!empty($this->request->data) && isset($this->request->data['FrankoTransaction']['request'])) {
			# request
			$this->FrankoTransaction->set($this->request->data);
			if ($this->FrankoTransaction->validates()) {
				$this->Common->flashMessage('Displayed', 'success');
			} else {
				$this->Common->flashMessage('formContainsErrors', 'error');
			}

		} elseif (!empty($this->request->data) && isset($this->request->data['FrankoTransaction']['move'])) {
			# move
			if ($this->FrankoTransaction->move($this->request->data)) {
				$this->Common->flashMessage('Transfer complete', 'success');
				$this->redirect(array('action'=>'transfer'));
			} else {
				$this->Common->flashMessage('formContainsErrors', 'error');
			}

		} elseif (!empty($this->request->data) && isset($this->request->data['FrankoTransaction']['send'])) {
			# send
			try {
				if ($this->FrankoTransaction->send($this->request->data)) {
					$this->Common->flashMessage('Transfer complete', 'success');
					$this->redirect(array('action'=>'transfer'));
				} else {
					$this->Common->flashMessage('formContainsErrors', 'error');
				}
			} catch (FrankoClientException $e) {
			$this->Common->flashMessage($e->getMessage(), 'error');
		}
		}

		if (empty($this->request->data)) {
			$this->request->data['Franko']['own_account_id'] = $this->FrankoTransaction->ownAccount();
			if ($address = $this->FrankoTransaction->ownAddress($addresses)) {
				$this->request->data['FrankoTransaction']['address'] = $address;
			} elseif ($address = $this->FrankoTransaction->Franko->getNewAddress()) {
				$this->Common->flashMessage('New Franko Address generated', 'info');
				$this->request->data['FrankoTransaction']['address'] = $address;
			}
		}

		$infos = $this->FrankoTransaction->Franko->getInfo();
		$this->Common->loadHelper(array('Tools.QrCode'));
		$this->set(compact('ownAccounts', 'ownAddresses', 'infos'));
	}

	/**
	 * transaction details
	 * 2011-07-19 ms
	 */
	public function admin_tx($txid = null) {
		if (empty($txid) || !$this->FrankoTransaction->Franko->validateTransaction($txid)) {
			$this->Common->flashMessage('Invalid Transaction', 'error');
			$this->redirect(array('action'=>'transfer'));
		}
		$transaction = $this->FrankoTransaction->Franko->getTransaction($txid);
		//e5b0f6297fa6743e0c2126fe5bda7b894a95bae7aae37d2695756b68468e4732
		$this->set(compact('txid', 'transaction'));
	}

	/**
	 * address details
	 * 2011-07-19 ms
	 */
	public function admin_address($address = null) {
		if (empty($address) || !$this->FrankoTransaction->Franko->validateAddress($address)) {
			$this->Common->flashMessage('Invalid Address', 'error');
			$this->redirect(array('action'=>'transfer'));
		}
	}


	public function admin_transactions($account = null) {
		if (!empty($this->request->params['named']['account'])) {
			$account = $this->request->params['named']['account'];
		}

		$transactions =	$this->FrankoTransaction->Franko->listTransactions($account);
		$accounts = $this->FrankoTransaction->accountList();
		$this->set(compact('accounts', 'transactions'));
	}

	public function admin_fee() {
		if ($this->Common->isPosted()) {
			$this->FrankoTransaction->set($this->request->data);
			if ($this->FrankoTransaction->validates() && ($amount = $this->FrankoTransaction->data['FrankoAddress']['amount']) >= 0 && $this->FrankoTransaction->Franko->setFee($amount)) {
				$this->Common->flashMessage('Changed', 'success');
			} else {
				$this->Common->flashMessage('formContainsErrors', 'error');
			}
		}

		$infos = $this->FrankoTransaction->Franko->getInfo();
		$this->set(compact('infos'));
	}

	/**
	 * manually trigger the cronjobbed tasks
	 * 2011-07-20 ms
	 */
	public function admin_run() {
		if ($this->FrankoTransaction->update()) {
			$this->log('Tasks manually triggered and successfully completed', 'franko');
			$this->Common->flashMessage('Tasks manually triggered and successfully completed', 'success');
		} else {
			$this->log('Tasks manually triggered but aborted', 'franko');
		}
		$this->Common->autoRedirect(array('action'=>'index'));
	}

/****************************************************************************************
 * protected/internal functions
 ****************************************************************************************/





/****************************************************************************************
 * deprecated/test functions
 ****************************************************************************************/


}
