<?php
namespace base;

/**
* Autoloader for homepage-Namespace
*
* @package base
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Autoloader {

	/**
	* Autoload Method
	*
	* @static
	* @access public
	* @throws FileNotFoundException
	*/
	public static function autoload($className) {

		$tmp = explode("\\", $className);

		$className = lcfirst(array_pop($tmp)) . '.php';
		$fileName = PATH_SRC . DS . join(DS, $tmp) . DS . $className;

		if(file_exists($fileName)) {

			require_once($fileName);
		}
	}
}

spl_autoload_register(__NAMESPACE__ . '\Autoloader::autoload');

?>
