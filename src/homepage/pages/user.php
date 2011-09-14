<?php
namespace homepage\pages;

use \base\Page;

use homepage\Request;
use homepage\UserManager;

/**
* Handler for homepage/user.php
*
* @package homepage.pages
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class User extends Page {

	/**
	* PageLogic
	*
	* @access public
	*/
	public function process() {

		// nothing to do - plain view
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

		if($request->get('logout')) {

			unset($_SESSION['loggedin']);
			unset($_SESSION['user']);

			$this->setFlash('Du hast dich erfolgreich ausgeloggt');
			$this->setRedirect('home');
			return true;
		}

		if($request->isPost()) {

			if($request->get('login', Request::POST)) {

				$username = $request->get('username', Request::POST);
				$password = $request->get('password', Request::POST);

				$user = new UserManager($username);

				if(!$user->exists()) {

					$errors[] = 'Dein Benutzername wurde nicht gefunden';
				} else {

					if($user->login($password)) {

						$_SESSION['loggedin'] = true;
						$_SESSION['user'] = $username;

						$this->setFlash('Erfolgreich eingeloggt');
						$this->setRedirect('home');
					} else {

						$this->setErrorFlash('Dein Passwort ist nicht korrekt');
					}
				}
			}
		}

		return false;
	}	
}

?>
