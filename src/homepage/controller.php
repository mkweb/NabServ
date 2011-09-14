<?php
namespace homepage;

use \base;

/**
* class Controller
*
* Handling Homepage-Requests
*
* @package homepage
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Controller extends base\Controller {

	/**
	* Reading Filerequests
	*
	* Every requested file does not exist physicaly, these are Classes in demo\filerequest
	* This Method is triggering execute() of these classes
	*
	* @access public
	*/
	public function handle() {

		if(count($this->request) == 0) {

			$this->request = array('index.php');
		}

		$base = ucfirst(array_shift($this->request));
		
		if(substr($base, -4) == '.php') {

			$base = substr($base, 0, -4);
		}

		$className = 'homepage\\filerequest\\' . $base;

		$file = new $className;
		$file->execute();
	}
}

?>
