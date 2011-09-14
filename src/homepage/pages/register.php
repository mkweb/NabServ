<?php
namespace homepage\pages;

use \base\Page;
use \base\LookupTable;

use \homepage\Request;
use \homepage\UserManager;

/**
* Handler for homepage/rabit.php
*
* @package homepage.pages
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Register extends Page {

	/**
	* Page-Title
	*
	* @access public
	* @var String
	*/
	public $title = 'Registrieren';

	/**
	* PageLogic
	*
	* @access public
	*/
	public function process() {

		// nothing to do - static view
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

		if($request->isPost()) {

			if($request->get('register', Request::POST)) {

				$username = $request->get('username', Request::POST);
				$password = $request->get('password', Request::POST);

				$user = new UserManager($username);
				$user->setData('password', $password);
				$user->setData('registered', time());

				if($user->validate()) {

					$data = array(
						'username' => $user->getData('username')
					);

					$table = new LookupTable('user');
					$table->add($data);

					$user->register();

					$this->setFlash('Registrierung erfolgreich');
					return true;
				} else {

					$errors = $user->getValidationErrors();
				}
			}
		}
	}
}

?>
