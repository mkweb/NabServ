<?php
namespace demo\pages;

use \base;

/**
* Mainpage for Demos
*
* @package demo.pages
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Main extends base\Page {

	/**
	* PageLogic
	*
	* @todo describe better for doc
	*
	* @access public
	*/
	public function process() {

		$current = $this->request->get('pl');
	
		$files = glob(PATH_SRC . DS . 'nabserv' . DS . 'plugins' . DS . '*');

		$plugins = array();
				
		foreach($files as $file) {

			$className = ucfirst(substr($file, strrpos($file, '/') + 1, -4));

			if($className != 'Plugin') {

				$name = $className;

				$className = "nabserv\\plugins\\" . $className;
				$plugin = new $className;

				if(method_exists($plugin, 'demo')) {

					$plugins[] = lcfirst($name);
				}
			}
		}

		$this->set('current', $current);
		$this->set('plugins', $plugins);
	}	
}

?>
