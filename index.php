<?php
error_reporting(E_ALL|E_STRICT);

function pr($value) {
	echo '<pre>';
	print_r($value);
	echo '</pre>';
}
session_start();

$tmp = explode('/', $_SERVER['SERVER_PROTOCOL']);

$protocol = strtolower($tmp[0]);
$hostname = rtrim($_SERVER['HTTP_HOST'], '/');
$uri	  = trim(dirname($_SERVER['SCRIPT_NAME']), '/');

$basehost = $hostname;
if($uri != '') {

	$basehost .= '/' . $uri;
}

$baseurl = sprintf("%s://%s", $protocol, $basehost);

define('DS', DIRECTORY_SEPARATOR);

define('PATH_ROOT', dirname(__FILE__));

define('BASE_HOST', $basehost);
define('BASE_URL', $baseurl);

define('PATH_SRC',   PATH_ROOT . DS . 'src');
define('PATH_LOG',   PATH_ROOT . DS . 'logs');
define('PATH_FILES', PATH_ROOT . DS . 'files');
define('PATH_RES',   PATH_ROOT . DS . 'res');
define('PATH_DB',    PATH_ROOT . DS . 'database');
define('PATH_TEMPLATES',    PATH_ROOT . DS . 'templates');

if(isset($_GET['recordfile'])) {

	$file = $_GET['recordfile'];

	$remove = (substr($file, 0, 4) == 'tmp_' ? true : false);
	
	$file = PATH_FILES . DS . 'records' . DS . $file;

	if(file_exists($file)) {

		header('Content-Type: audio/x-wav');
		readfile($file);
	}

	if($remove) unlink($file);
	exit;
}

require_once('src/base/autoload.php');

$data = $_GET;
$request = explode('/', trim(array_shift($data), '/'));

$configFile = PATH_RES . DS . 'config.php';
\base\Config::init($configFile);
\base\Lang::init(\base\Config::read('lang'));

$controller = new base\Controller();
$controller->setRequest($request, $data);

$controller->handle();

?>
