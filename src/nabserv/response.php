<?php
namespace nabserv;

use \base\Logger;

/**
* Responsehandler 
*
* In this class Ping-Response to Nabaztag is generated
* 
* @package nabserv
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Response {

	/**
	* Default start-byte 7F
	* @param Array
	*/
	private $start 	= array('7F');

	/**
	* Default end-bytes FF0A
	* @param Array
	*/
	private $end 	= array('FF', '0A');

	/**
	* Array with hexadecimal representation of response
	* @param Array 
	*/
	private $data 	= array();

	/**
	* Returns Array with whole hexadecimal representation of response
	*
	* @access public
	*
	* @return Array
	*/
	public function getHex() {

		$result = array();

		foreach($this->start as $char) {

			$result[] = $char;
		}

		foreach($this->data as $char) {

			$result[] = $char;
		}
		
		foreach($this->end as $char) {

			$result[] = $char;
		}

		return $result;
	}

	/**
	* Reads data from given plugin and appends them to response
	*
	* @access public
	*
	* @param Plugin
	*/
	public function addPlugin(plugins\Plugin $plugin) {

		$data = $plugin->getData();

		foreach($data as $d) {

			$this->data[] = $d;
		}
	}

	/**
	* Prints binary response
	*
	* @access public
	*/
	public function __toString() {

		$result = '';
		$hex = $this->getHex();

		Logger::debug(__CLASS__ . ' - Hex: ' . join(" ", $hex));

		foreach($hex as $h) {

			$result .= chr(hexdec($h));
		}

		return $result;
	}
}

?>
