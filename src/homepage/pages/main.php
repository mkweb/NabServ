<?php
namespace homepage\pages;

use \base\Page;
use \base\Lang;

/**
* Handler for the homepage MainPage
*
* @package homepage.pages
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Main extends Page {

	/**
	* PageLogic
	*
	* @access public
	*/
	public function process() {

		if(!isset($_SESSION['loggedin'])) {

			$nav = array(
				Lang::get('navi.home') => '/?page=home',
				Lang::get('navi.register') => '/?page=register'
			);
		} else {

			$nav = array(
				Lang::get('label.overview') => '/?page=home', 
				Lang::get('rabits.headline') => '/?page=rabits',
				Lang::get('things.headline') => '/?page=things'
			);
		}

		$this->set('translations', Lang::getAll());
		$this->set('nav', $nav);
		$this->set('adsense', true);
	}	
}

?>
