# CakePHP Franko Payment Plugin

Containing **Franko IPN** (Franko Instant Payment Notification)

* Version 1.0
* Author: Mark Scherer | Christopher Franko
* Website: http://www.dereuromark.de | Frankos.org
* License: MIT License (http://www.opensource.org/licenses/mit-license.php)

## Requirement
CakePHP 2.x
Franko 0.8.4.1

## TODOS:
* full non-daemon "offline" mode? using the webservice completely without any local daemon necessary
* add other payment methods
* make it more independable (right now needs some of MY tools plugin stuff for the admin interface to work) - the lib itself should work just fine.


## Installation

* Clone/Copy the files in this directory into `app/Plugin/Payment`
* Don't forget to include the plugin in your bootstrap's `CakePlugin::load()` statement or use `CakePlugin::loadAll()`
* Run

		$ cake schema create payment -plugin payment


# Franko Setup:
1. Download program at http://www.frankos.org/ for testing purposes
2. Set up frankod daemon on your webserver (this is the most difficult step if you don't use the newest system)
3. Get some coins :)
4. Provide a config array in your configs: $config['Franko'] = array(..) with your preferences and credentials

	### important ones are:

	* account
	* username
	* password

## Administration (optional)
If you want to use the built in admin access to IPNs:

1. Make sure you're logged in as an Administrator via the Auth component.
2. Navigate to `www.yoursite.com/admin/payment/franko`


## Franko Notification Callback:
Create a function in your `/app/AppModel.php` like so:

	public function afterFrankoNotification($txnId){
		//Here is where you can implement code to apply the transaction to your app.
		//for example, you could now mark an order as paid, a subscription, or give the user premium access.
		//retrieve the transaction using the txnId passed and apply whatever logic your site needs.

		$transaction = ClassRegistry::init('Payment.FrankoAddress')->findById($txnId);
		$this->log($transaction['FrankoAddress']['id'], 'franko');

		if(...) {
			//Yay!  We have the money!
		}	else {
			//Oh no, not enough... better look at this transaction to determine what to do; like email a decline letter.
		}
	}

## Franko Helper: (optional)
1. Add `Payment.Franko` to your helpers list in `AppController.php`

	public $helpers = array('Html','Form','Payment.Franko');

### Usage: (view the actual /View/Helpers/FrankoHelper.php for more information)
		$this->Franko->image(64);

		$this->Franko->paymentBox(12.3, YOUR_ADDRESS);


# Tips
* The Lib itself has offline capacities. It is possible to use certain features even without having to run a local franko daemon (at least for testing purposes on a local machine).
* Create your own admin interface for the payment methods. You can use the existing one as a template.
* Start playing around with "little" money. If sth goes wrong, your money might get lost.
* Use every franko address only once! The default implementation will check only check the amount received. It can not (protocol!) distinguish between different people sending money to those addresses. therefore it cannot be used more than once if you don't want conflicts.

# Final notes
As tempting as it was to integrate the Paypal IPN Plugin which this plugin is based on, I decided not to do this at the moment.
This way they can be maintained separately.
Not only this plugin but also the technology/protocol itself is under heavy maintenance and still changing from time to time.

It could become a complete "Payment" plugin combining all methods and services one day...

I spent quite a few days developing this plugin and testing all features.
Feel free to donate if you use this plugin

* Address: 161AcnPykE42e4ErQNR9B73Bb78Jy81AN6

Enjoy!