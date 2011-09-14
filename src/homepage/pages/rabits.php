<?php
namespace homepage\pages;

use \base\Page;

use \homepage;

use base\LookupTable;

use homepage\UserManager;
use homepage\Request;

/**
* Handler for homepage/rabit.php
*
* @package homepage.pages
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Rabits extends Page {

	/**
	* Page-Title
	*
	* @access public
	* @var String
	*/
	public $title = 'Deine Hasen';

	/**
	* Force Controller to check if user is logged in
	*
	* @access protected
	* @var boolean
	*/
	protected $protected = true;

	/**
	* Store for validation error message
	*
	* @access private
	* @var Array
	*/
	private $validationErrors = array();

	/**
	* PageLogic
	*
	* @access public
	*/
	public function process() {

		if(!isset($_SESSION['loggedin'])) {

			return false;
		}

		$this->set('user', new UserManager($_SESSION['user']));

		if(count($this->validationErrors) > 0) {

			$this->set('validationErrors', $this->validationErrors);
		}

		$this->set('name', $this->request->get('name', Request::POST));
		$this->set('serial', $this->request->get('serial', Request::POST));
	}

	/**
	* Process actions before building view
	* If this Methods returns true, MVC-Controller will force Browser to refresh
	*
	* @access public
	*
	* @return boolean
	*/
	public function processAction() {

		$request = $this->request;

		if(isset($_GET['nab_remove'])) {

			$serial = $request->get('nab_remove');

			$user = new UserManager($_SESSION['user']);
			$res = $user->removeNabaztag($serial);

			if(!$res) {

				$this->setErrorFlash("Dieser Hase kann nicht gelöscht werden. Evtl. gehört er dir nicht?");
			} else {

				$this->setRedirect('rabits');
				$this->setFlash("Der Hase wurde erfolgreich gelöscht.");
				return true;
			}
		}
		
		if($request->isPost()) {

			if($request->get('nab_create', Request::POST)) {

				$name = $request->get('name', Request::POST);
				$serial = $request->get('serial', Request::POST);

				$errors = array();
				if(strlen($name) < 1) {

					$errors['name'] = 'Bitte gib einen Namen an';
				}
				
				if(strlen($serial) < 1) {

					$errors['serial'] = 'Bitte gib die Seriennummer an';
				}
		
				$lookup = LookupTable::getInstance('nabaztag');

				if($lookup->find(array('name' => $name))) {

					$errors['name'] = 'Dieser Name ist bereits vergeben';
				}

				if($lookup->find(array('serial' => $serial))) {

					$errors['serial'] = 'Diese Seriennummer ist bereits registriert';
				}

				if(count($errors) > 0) {

					$this->validationErrors = $errors;

					$this->setErrorFlash(join('<br />', $errors));
					return false;
				}

				$user = new UserManager($_SESSION['user']);
				$res = $user->createNabaztag($serial, $name);

				if(!$res) {

					$this->setErrorFlash("Dieser Hase ist schon registriert.");
				} else {

					$this->setFlash("Der Hase wurde erfolgreich angelegt.");
					return true;
				}
			}
		}

		return false;
	}
}
