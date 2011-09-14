<?php
namespace nabserv\filerequest;

use \nabserv;
use \base\Logger;

use nabserv\Request;
use nabserv\Nabaztag;

use \Exception;

/**
* @package nabserv.filerequest
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Api {

	public function execute() {

		$request = new Request();

		Logger::debug("New API-Call detected");

		header('Content-Type: text/xml');

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

			$api = new nabserv\Api($request);

			if(($notfound = $api->loadPlugins()) != null) {

				echo '<NabServ><Api><result>false</result><details>Plugin ' . $notfound . ' not found</details></Api></NabServ>';
				exit;
			}

			if($api->handle()) {

				echo '<NabServ><Api><result>true</result></Api></NabServ>';
			} else {

				echo '<NabServ><Api><result>false</result></Api></NabServ>';
			}
			
		} catch (Exception $e) {

			echo '<NabServ><Api><result>false</result><details>Exception ' . get_class($e) . ": " . $e->getMessage() . '</details></Api></NabServ>';
		}
	}
}

?>
