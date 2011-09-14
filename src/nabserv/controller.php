<?php
namespace nabserv;

use \base;

use nabserv\Nabaztag;

/**
* @package nabserv
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Controller extends base\Controller {

	public function handle() {

		if(isset($this->data['sn'])) {

			$serial = $this->data['sn'];

			$nab = Nabaztag::getInstance($serial);
			$token = $nab->getConfig('token');

			if(!defined('NAB_SERIAL')) define('NAB_SERIAL', $serial);
			if(!defined('NAB_TOKEN'))  define('NAB_TOKEN', $token);
		}

		if(count($this->request) > 0) {

			$base = ucfirst(array_shift($this->request));
			
			if(in_array(substr($base, -4), array('.php', '.jsp'))) {

				$base = substr($base, 0, -4);
			}

			$className = 'nabserv\\filerequest\\' . $base;

			$file = new $className;
			$file->execute();
		}
	}
}

?>
