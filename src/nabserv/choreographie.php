<?php
namespace nabserv;

use \base\Logger;

/**
* Class Choreographie
*
* Creating an binary Choreographie-File
*
* @tutorial http://www.cs.uta.fi/hci/spi/jnabserver/documentation/index.html#choreographies
*
* @package nabserv
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Choreographie {

	/**
	* Cache-array for hexadecimal representation of chor
	* @var Array
	*/
	private $hex = array();

	/**
	* Directory where chors are saved
	* @var String
	*/
	private $dirname;

	/**
	* Constructor
	* 
	* @access public
	*
	* @param Int
	*/
	public function __construct($tempo) {

		$this->dirname = PATH_FILES . DS . 'chor' . DS . 'choreographies';
		$this->addTempo($tempo);
	}

	/**
	* Adds Tempo to result
	*
	* @access public
	* 
	* @param Int
	*/
	public function addTempo($tempo) {

		$tempo = dechex($tempo);

		$this->hex[] = '00';	// empty byte
		$this->hex[] = '01';	// typeCode
		$this->hex[] = $tempo;
	}

	/**
	* Adds LED-Command to chor
	*
	* @access public
	*
	* @param Int Led from 0 to 4 (bottom, right, center, left, nose)
	* @param String hexadecimal Colorvalue (6-digit)
	* @param Int Time to wait after last action
	*/
	public function addLed($led, $color, $wait = 0) {

		$led = dechex($led);
		$color = str_split(ltrim($color, '#'), 2);
		
		$this->hex[] = dechex($wait);	// wait
		$this->hex[] = '07';		// typeCode
		$this->hex[] = $led;
		$this->hex[] = $color[0];
		$this->hex[] = $color[1];
		$this->hex[] = $color[2];
		$this->hex[] = '00';		// empty byte
		$this->hex[] = '00';		// empty byte
	}

	/**
	* Adds EAR-Command to chor
	*
	* @access public
	*
	* @param Int Ear (0=right,1=left)
	* @param Int Position (0 - 180 degree)
	* @param Int Direction (0=forward, 1=backward)
	* @param Int Time to wait after last action
	*/
	public function addEar($ear, $pos, $dir, $wait = 0) {

		$ear = sprintf('%02s', dechex($ear));
		$pos = sprintf('%02s', dechex($pos));
		$dir = sprintf('%02s', dechex($dir));

		$this->hex[] = dechex($wait);	// wait
		$this->hex[] = '08';		// typeCode
		$this->hex[] = sprintf('%02s', $ear);
		$this->hex[] = sprintf('%02s', $pos);
		$this->hex[] = sprintf('%02s', $dir);
	}

	/**
	* Returns array with hexadecimal representation of chor
	* 
	* @access public
	*
	* @return Array
	*/
	public function getHex() {

		$hex = array();
		$hex[] = '00';
		$hex[] = '00';
		$hex[] = '01';
		$hex[] = dechex(count($this->hex));

		$hex = array_merge($hex, $this->hex);
		
		$hex[] = '00';
		$hex[] = '00';
		$hex[] = '00';
		$hex[] = '00';

		return $hex;
	}

	/**
	* Returns binary representation of chor
	*
	* @access public
	*
	* @retrun String
	*/
	public function __toString() {

		$string = '';
		$hex = $this->getHex();

		foreach($hex as $h) {

			$string .= chr(hexdec($h));
		}

		return $string;
	}

	/**
	* Saves chor in default direction 
	* 
	* @access public
	*
	* @param String Filename without .chor
	*/
	public function save($name) {

		Logger::debug("saving choregraphie $name");

		$fh = fopen($this->dirname . DS . $name . '.chor', 'w');
		fputs($fh, $this);
		fclose($fh);
	}
}

?>
