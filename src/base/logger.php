<?php
namespace base;

/**
* Class Logger
* Simple logging utility
*
* Logs are stored in Date-Direction under PATH_LOG
*
* @use PATH_LOG
*
* @package base
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Logger {

	/**
	* Current Loglevel
	*
	* @static
	* @access private
	* @var Int
	*/
	private static $level  = 0;

	/**
	* Current LogTarget
	* Only LOG_FILE is implemented
	*
	* @static
	* @access private
	* @var Int
	*/
	private static $target = 5;

	/**
	* Default Filename if none given
	*
	* @static
	* @access private
	* @var String
	*/
	private static $filename = 'default';

	const LEVEL_FATAL = 0;
	const LEVEL_WARN  = 1;
	const LEVEL_INFO  = 2;
	const LEVEL_DEBUG = 3;

	const LOG_SCREEN  = 4;
	const LOG_FILE 	  = 5;

	/**
	* Setter for LogLevel
	*
	* @access pulic
	* @static
	*
	* @param Int
	*/
	public static function set($level) {

		self::$level = $level;
	}

	/**
	* Setter for Target-Filename without path or .php
	*
	* @access public
	* @static
	*
	* @param String
	*/
	public static function setFileName($filename) {

		self::$filename = $filename;
	}

	/**
	* Loggs Fatal-Errors if Level greater or eqals 0
	*
	* @access public
	* @static
	*
	* @param String
	* @param String [optional]
	*/
	public static function fatal($msg, $filename = null) {

		if(self::$level >= self::LEVEL_FATAL) {

			self::log($msg, 'FATAL', $filename);
			exit;
		}
	}

	/**
	* Loggs Warnings if Level greater or eqals 1
	*
	* @access public
	* @static
	*
	* @param String
	* @param String [optional]
	*/
	public static function warn($msg, $filename = null) {

		if(self::$level >= self::LEVEL_WARN) {

			self::log($msg, 'WARN', $filename);
		}
	}

	/**
	* Loggs Infos if Level greater or eqals 2
	*
	* @access public
	* @static
	*
	* @param String
	* @param String [optional]
	*/
	public static function info($msg, $filename = null) {

		if(self::$level >= self::LEVEL_INFO) {

			self::log($msg, 'INFO', $filename);
		}
	}

	/**
	* Loggs Debug-Informations if Level greater or eqals 3
	*
	* @access public
	* @static
	*
	* @param String
	* @param String [optional]
	*/
	public static function debug($msg, $filename = null) {

		if(self::$level >= self::LEVEL_DEBUG) {

			self::log($msg, 'DEBUG', $filename);
		}
	}

	/**
	* This Method does the logging-action
	*
	* @access public
	* @static
	*
	* @param String
	* @param Int
	* @param String
	*/
	private static function log($msg, $level, $filename) {

		if(is_null($filename)) {

			$filename = self::$filename;
		} 

		$basepath = PATH_LOG;
		$path = date('Y/m/d');

		$tmp = explode("/", $path);

		$currentPath = $basepath;
		foreach($tmp as $step) {

			$currentPath .= '/' . $step;
			if(!file_exists($currentPath)) {

				mkdir($currentPath, 0777);
			}
		}

		$filename = $currentPath . '/' . $filename . '.log';
		$all = $currentPath . '/all.log';

		$template = '%s - [%s] - %s';
		$msg = sprintf($template, date('Y-m-d H:i:s'), $level, $msg);

		$fh = fopen($filename, 'a+');
		fputs($fh, $msg . "\n");
		fclose($fh);

		if(isset($all)) {

			$fh = fopen($all, 'a+');
			fputs($fh, $msg . "\n");
			fclose($fh);
		}
	}
}

?>
