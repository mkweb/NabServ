<?php
namespace nabserv\filerequest;

use \Exception;

use \nabserv;
use \base\Logger;

use nabserv\Nabaztag;
use nabserv\ActionProcessor;
use nabserv\Request;
use nabserv\Ping;

/**
* @package nabserv.filerequest
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Rfid {

	public function execute() {

		$request = new Request();
		$rfid_id = $request->getData('t');

		$ap = new ActionProcessor();
		$ap->rfid($rfid_id);
	}
}

?>
