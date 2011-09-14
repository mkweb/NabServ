<?php
namespace nabserv\filerequest;

use \base\Logger;

use nabserv\Nabaztag;

/**
* @package nabserv.filerequest
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Locate {

	public function execute() {
		Logger::set(Logger::LEVEL_DEBUG);

		echo "ping " . BASE_HOST . "\nbroad " . BASE_HOST;

		$nab = Nabaztag::getInstance($_GET['sn']);

		$sn = $_GET['sn'];
		$token = $nab->getConfig('token');

		$url = BASE_URL . "/vl/api.php?sn=" . $sn . "&token=" . $token . "&pingInterval=3";
		file_get_contents($url);
	}
}
?>
