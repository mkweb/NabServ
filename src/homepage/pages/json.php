<?php
namespace homepage\pages;

use \base\Page;
use \base\LookupTable;

use homepage\UserManager;
use nabserv\Nabaztag;

/**
* Handler for homepage/json.php
*
* This file is called via REST and is used as Ajax-Backend for Nabaztag-Calls
* Methodname must be given by GET-Var 'm'
*
* @package homepage.pages
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Json extends Page {

	/**
	* Page-Title
	*
	* @access public
	* @var String
	*/
	public $title = '';

	/**
	* PageLogic
	*
	* Token and Serial must be correct.
	* Nabaztag-Object is given to every Method
	*
	* @access public
	*/
	public function process() {

		$serial = $this->request->get('sn');
		$token = $this->request->get('token');
		$method = $this->request->get('m');

		$rabit = Nabaztag::getInstance($serial);

		if($rabit->getConfig('token') != $token) {

			echo "Wrong token"; 
			exit;
		}

		if(method_exists($this, $method)) {

			$this->$method($rabit);
		} else {

			echo "Method " . $method . " does not exist";
			exit;
		}
		exit;
	}

	/**
	* Prints all Configuration for given Nabaztag
	*
	* @access public
	*
	* @param nabserv\Nabaztag
	*/
	public function getconfig($rabit) {

		$config = $rabit->getAllConfig();

		echo json_encode($config);
	}

	/**
	* Prints all Stored Things for Session-User
	*
	* @access public
	*/
	public function getthings() {

		$user = new UserManager($_SESSION['user']);
		echo json_encode($user->getData('things'));
	}

	public function changeName() {

		$serial = $this->request->get('sn');
		$newname = $this->request->get('newname');

		if(!$newname || strlen($newname) < 1) {

			$this->returnError('Bitte gib einen Namen an');
		}

		$table = LookupTable::getInstance('nabaztag');
		$nabdata = $table->find(array('serial' => $serial));

		if(isset($nabdata['username'])) {

			$user = new UserManager($nabdata['username']);
			if(!$user->updateNabaztagConfig($nabdata['serial'], array('name' => $newname))) {

				$this->returnError($user->getLastError());
			}
		}
	}

	private function returnError($msg) {

		echo '__error__' . $msg;
		exit;
	}
}

?>
