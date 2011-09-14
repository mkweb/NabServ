<?php
namespace homepage\pages;

use \base\Page;
use \base\Lang;

use homepage\UserManager;
use homepage\Request;

use nabserv\Nabaztag;

/**
* Handler for homepage/js.php
*
* @package homepage.pages
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Js extends Page {

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
	* @access public
	*/
	public function process() {

		$file = PATH_ROOT . DS . $this->request->get('file') . '.js';

		if(file_exists($file)) {

			$content = file_get_contents($file);

			$content = Lang::replace($content);

			echo $content;
		}
		exit;
	}
}

?>
