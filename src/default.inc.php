<?php
error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', true);

if(!function_exists('pr')) {
	function pr($value) {
		echo '<pre>';
		print_r($value);
		echo '</pre>';
	}
}

if(!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}

define('API_BASE_PATH', dirname(dirname(__FILE__)));

require_once(API_BASE_PATH . DS . 'src/nabserv/autoload.php');

?>
