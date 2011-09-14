<?php
namespace nabserv\filerequest;

use \base\Logger;

/**
* @package nabserv.filerequest
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Record {

	public function execute() {
		Logger::set(Logger::LEVEL_DEBUG);

		$filename = PATH_FILES . DS . 'records' . DS . 'tmp_' . md5(microtime()) . '.wav';

		file_put_contents($filename, file_get_contents("php://input", true));

		Logger::debug($filename);

		$tmp = explode("/", $filename);
		$filename = array_pop($tmp);

		$string = "ID " . rand(11111, 99999) . "\nMU " . BASE_URL . "/records/" . $filename;

		$url = BASE_URL . "/vl/api.php?sn=" . NAB_SERIAL . "&token=" . NAB_TOKEN . "&message=" . urlencode($string);
		file_get_contents($url);
	}
}
?>
