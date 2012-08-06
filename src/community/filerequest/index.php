<?php
namespace community\filerequest;

use base\mvc\Controller;
use homepage\Request;

/**
* Processing Request for community/index.php
*
* @package community.filerequest
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Index {

	/**
	* Triggering Demo-MVC
	*
	* @access public
	*/
	public function execute() {

		$request = new Request();
		new Controller($request);
	}
}
?>
