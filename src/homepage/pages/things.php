<?php
namespace homepage\pages;

use \base\Page;

use \homepage\UserManager;

use nabserv\Nabaztag;

/**
* Handler for homepage/rabit.php
*
* @package homepage.pages
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Things extends Page {

	/**
	* Page-Title
	*
	* @access public
	* @var String
	*/
	public $title = 'Dinge';

	/**
	* Force Controller to check if user is logged in
	*
	* @access protected
	* @var boolean
	*/
	protected $protected = true;

	/**
	* PageLogic
	*
	* @access public
	*/
	public function process() {

		$things = array();

		$serial = $token = $name = null;

		if($this->request->get('sn')) {

			$serial = $this->request->get('sn');
			$rabit = Nabaztag::getInstance($serial);
		
			$serial = $rabit->getSerial();
			$token  = $rabit->getConfig('token');
			$name   = $rabit->getConfig('name');
		}

		$um = new UserManager($_SESSION['user']);
		
		if($this->request->get('add')) {

			$id = $this->request->get('add');
			$name = $this->request->get('name');
		
			$current = $um->getData('things');

			if(is_null($current)) {

				$current = array();
			}

			$current[] = array('id' => $id, 'name' => $name);

			$um->setData('things', $current);
			$um->save();

			header('Location: /?page=things&sn=' . $serial);
		}

		$things = $um->getData('things');

		
		$this->set('serial', $serial);
		$this->set('token', $token);
		$this->set('name', $name);
		$this->set('rabits', $um->getRabits());
		$this->set('things', $things);
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

		if($request->get('thing_remove')) {

			$um = new UserManager($_SESSION['user']);

			$current = $um->getData('things');

			foreach($current as $key => $thing) {

				if($thing['id'] == $request->get('thing_remove')) {

					unset($current[$key]);
					$um->setData('things', $current);
					$um->save();

					$this->setRedirect('things');
					$this->setFlash('Das Ding wurde erfolgreich gelöscht');
					return true;
				}
			}
		}

		return false;
	}
}

?>
