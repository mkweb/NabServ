<?php
namespace nabserv\filerequest;

use \nabserv;
use \base\Logger;

use nabserv\Request;
use nabserv\Nabaztag;
use nabserv\AppController;

use \Exception;

/**
* @package nabserv.filerequest
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class App {

	public function execute() {

		$request = new Request();

		try {
			$token = $request->getData('token');

			if(is_null($token)) {

				throw new Exception('No Token specified');
			} else {

				$nab = Nabaztag::getInstance($request->getData('sn'));
				
				if($token != $nab->getConfig('token')) {

					throw new Exception('Token is not valid');
				}
			}

			$data = $request->getData('d');

			$controller = new AppController($request->getData('sn'));
			$controller->handle($data);

		} catch (Exception $e) {

			echo '<NabServ><Api><result>false</result><details>Exception ' . get_class($e) . ": " . $e->getMessage() . '</details></Api></NabServ>';
		}
	}
}

?>
