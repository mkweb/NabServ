<?php
namespace homepage\pages;

use \base\Page;
use \base\Logger;

use homepage\Request;
use nabserv\Nabaztag;

/**
* Handler for homepage/rabit.php
*
* @package homepage.pages
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Rabit extends Page {

	/**
	* Page-Title
	*
	* @access public
	* @var String
	*/
	public $title = 'Hasen bearbeiten';

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

		$sectionMapping = array(
			'overview' 	=> '0',
			'apps'		=> '1',
			'message'	=> '2'
		);

		if(!isset($_SESSION['loggedin'])) {

			return false;
		}

		$section = $this->request->get('section');

		if(!is_null($section)) {

			$section = (isset($sectionMapping[$section]) ? $sectionMapping[$section] : 0);
		}

		$sn = $this->request->get('sn');

		$nab = new Nabaztag($sn);

		$name = $nab->getConfig('name');

		$this->title = $name;

		$url = BASE_URL . "/vl/app.php?sn=" . $sn . "&token=" . $nab->getConfig('token') . "&d=getall";
		$apps = json_decode(file_get_contents($url));

		$crontab = array();
		foreach($apps->inuse as $app) {

			if(isset($app->config->trigger->crontab)) {

				$crontab[$app->code] = $app->config->trigger->crontab;
			} else {

				$crontab[$app->code] = array();
			}
		}

		$action = array();
		foreach($apps->inuse as $app) {

			if(isset($app->config->trigger->action)) {

				$action[$app->code] = $app->config->trigger->action;
			} else {

				$action[$app->code] = array();
			}
		}

		$this->set('configApp', $this->request->get('configapp'));

		$this->set('nab', 	$nab);
		$this->set('section', 	$section);
		$this->set('apps', 	$apps);
		$this->set('name', 	$name);
		$this->set('serial', 	$sn);
		$this->set('token', 	$nab->getConfig('token'));
		$this->set('crontabs', 	$crontab);
		$this->set('actions', 	$action);
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

		if($this->request->get('useapp')) {

			$serial = $this->request->get('sn');
			$nab = Nabaztag::getInstance($serial);

			$code = $this->request->get('useapp');

			$url = BASE_URL . "/vl/app.php?sn=" . $serial . "&token=" . $nab->getConfig('token') . "&d=useapp," . $code;
			$result = json_decode(file_get_contents($url));

			if($result->result == true) {

				$this->setFlash('Das Progamm wurde erfolgreicht installiert');
			} else {

				$this->setErrorFlash('Fehler beim installieren');
			}

			$this->setRedirect('rabit&sn=' . $serial . '&section=apps');
			return true;
		}

		if($this->request->get('removeapp')) {

			$serial = $this->request->get('sn');
			$nab = Nabaztag::getInstance($serial);

			$code = $this->request->get('removeapp');

			$url = BASE_URL . "/vl/app.php?sn=" . $serial . "&token=" . $nab->getConfig('token') . "&d=removeapp," . $code;
			$result = json_decode(file_get_contents($url));

			if($result->result == true) {

				$this->setFlash('Das Progamm wurde erfolgreicht entfernt');
			} else {

				$this->setErrorFlash('Fehler beim entfernen');
			}

			$this->setRedirect('rabit&sn=' . $serial . '&section=apps');
			return true;
		}

		if($this->request->isPost()) {
	
			if($this->request->get('message', Request::POST)) {

				$message 	= $this->request->get('message', Request::POST);
				$sn 		= $this->request->get('sn');

				$nab = new Nabaztag($sn);
				$token = 	$nab->getConfig('token');

				$url = BASE_URL . "/vl/api.php?sn=" . $sn . "&token=" . $token . "&tts=" . urlencode($message);

				Logger::debug('calling API: ' . $url);
				$result = file_get_contents($url);
				Logger::debug('result: ' . $result);

				preg_match('/<result>(.*)<\/result>/Uis', $result, $res);

				if(isset($res[1]) && $res[1] == 'true') {

					$this->setFlash("Die Nachricht wurde erfolgreich versendet");
				} else {

					$this->setErrorFlash("Die Nachricht konnte leider nicht versendet werden");
				}

				$this->setRedirect('rabit&sn=' . $sn . '&section=message');
				return true;
			}
		}
	}
}

?>
