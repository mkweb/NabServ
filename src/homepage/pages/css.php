<?php
namespace homepage\pages;

use \base\Page;

use homepage\UserManager;
use homepage\Request;

use nabserv\Nabaztag;

/**
* Handler for homepage/css.php
*
* @package homepage.pages
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Css extends Page {

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

		$file = PATH_ROOT . DS . $this->request->get('file') . '.css';

		if(file_exists($file)) {

			$content = file_get_contents($file);
			$content = str_replace('[BASE_URL]', BASE_URL, $content);

			echo $content;
		}
		exit;
	}
}

?>
