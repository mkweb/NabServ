<?php
namespace homepage\filerequest;

use base\mvc\Controller;
use homepage\Request;

use nabserv\Nabaztag;

/**
* Processing Request for homepage/index.php
*
* @package homepage.filerequest
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Index {

	/**
	* Triggering Homepage-MVC
	*
	* @access public
	*/
	public function execute() {

		$request = new Request();
		new Controller($request);
	}
}

?>
