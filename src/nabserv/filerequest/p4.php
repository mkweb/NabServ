<?php
namespace nabserv\filerequest;

use \nabserv;
use \base\Logger;

use nabserv\ActionProcessor;
use nabserv\Ping;
use nabserv\Request;
use nabserv\Nabaztag;
use nabserv\AppController;

/**
* @package nabserv.filerequest
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class P4 {

	public function execute() {

		$request = new Request();

		Logger::info('====== STARTING REQUEST ========');
		Logger::debug("Request: " . print_r($request->getAllData(), true));

		if(!$request->isValid()) {
//			echo "No valid request / You're not allowed";
//			exit;
		}

		try {

			$nabaztag = Nabaztag::getInstance(NAB_SERIAL);
			$nabaztag->setConfig('lastseen', time());

			if($request->hasAction()) {

				$ap = new ActionProcessor($nabaztag);
				$ap->beforePing($request->getAction());
			}

			$appc = AppController::getInstance(NAB_SERIAL);
			$appc->loadApps();
			$appc->executePings();
			$appc->executeCrons();

			$ping = new Ping($request);
			$ping->loadPlugins();
			$ping->handle();

			if($request->hasAction()) {

				$ap = new ActionProcessor($nabaztag);
				$ap->afterPing($request->getAction());
			}

			$ping->sendResponse();

		} catch (Exception $e) {

			echo "Exception " . get_class($e) . ": " . $e->getMessage();
		}
	}
}

?>
