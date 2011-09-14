<?php
namespace nabserv\filerequest;

/**
* @package nabserv.filerequest
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Bc {

	public function execute() {

		$replace = array('record', 'p4', 'locate', 'rfid');

		$content = file_get_contents(PATH_FILES . DS . 'bootcode' . DS . 'bootcode.bin');

		foreach($replace as $r) {

			$content = str_replace($r . '.jsp', $r . '.php', $content);
		}

		echo $content;
	}
}

?>
