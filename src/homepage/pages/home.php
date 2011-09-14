<?php
namespace homepage\pages;

use \base\Page;

use homepage\UserManager;
use homepage\Request;

use nabserv\Nabaztag;

/**
* Handler for homepage/home.php
*
* @package homepage.pages
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Home extends Page {

	/**
	* Page-Title
	*
	* @access public
	* @var String
	*/
	public $title = 'Übersicht';

	/**
	* PageLogic
	*
	* @access public
	*/
	public function process() {

		if(!isset($_SESSION['loggedin'])) {

			return false;
		}

		$hasApps = false;

		$user = new UserManager($_SESSION['user']);
		$rabits = $user->getRabits();
	
		$apps = array();

		if(count($rabits) > 0) {
			foreach($rabits as $rabit) {

				$nab = Nabaztag::getInstance($rabit['serial']);

				$url = BASE_URL . "/vl/app.php?sn=" . $nab->getSerial() . "&token=" . $nab->getConfig('token') . "&d=getall";

				$app = json_decode(file_get_contents($url));

				$app = (Array) $app;
				$app['nabserial'] = $nab->getSerial();

				$apps[$nab->getConfig('name')] = $app;

				if(isset($app['inuse']) && count($app['inuse']) > 0) {

					$hasApps = true;
				}
			}
		}

		$things = $user->getData('things');

		$this->set('hasApps', $hasApps);	
		$this->set('user', $user);
		$this->set('rabits', $rabits);
		$this->set('apps', $apps);
		$this->set('things', $things);
	}
}

?>
