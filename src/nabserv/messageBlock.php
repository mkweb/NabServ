<?php
namespace nabserv;

use \Logger;

/**
* Class MessageBlock
*
* This class is used to generate a Messagecommand which Nabaztag can understand.
* 
* @tuturial http://www.cs.uta.fi/hci/spi/jnabserver/documentation/index.html#block_message
*
* @package nabserv
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class MessageBlock {

	/**
	* Singleton Instance
	*/
	private static $instance = null;

	/**
	* ID-Value
	*/
	private $id;

	/**
	* Array-representation of Message
	*/
	private $data = array();

	/**
	* Hexadecimal result
	*/
	private $hex = array();

	/**
	* Used to encode Message
	*/
	private $invtable = array( 1, 171, 205, 183, 57, 163, 197, 239, 241, 27, 61, 167, 41, 19, 53, 223, 225, 139, 173, 151, 25, 131, 165, 207, 209, 251, 29, 135, 9, 243, 21, 191, 193, 107, 141, 119, 249, 99, 133, 175, 177, 219, 253, 103, 233, 211, 245, 159, 161, 75, 109, 87, 217, 67, 101, 143, 145, 187, 221, 71, 201, 179, 213, 127, 129, 43, 77, 55, 185, 35, 69, 111, 113, 155, 189, 39, 169, 147, 181, 95, 97,11, 45, 23, 153, 3, 37, 79, 81, 123, 157, 7, 137, 115, 149, 63, 65, 235, 13, 247, 121, 227, 5, 47, 49, 91, 125, 231, 105, 83, 117, 31, 33, 203, 237, 215, 89, 195, 229, 15, 17, 59, 93, 199, 73, 51, 85, 255);

	/**
	* Singleton
	*
	* @static
	* @access public
	*
	* @param Int optional
	*
	* @return MessageBlock
	*/
	public static function getInstance($id = null) {

		if(is_null(self::$instance)) {

			self::$instance = new MessageBlock($id);
		} 

		if(!is_null($id)) {

			self::$instance->setId($id);
		}

		return self::$instance;
	}

	/**
	* Constructor
	*
	* @access public
	*
	* @param Int optional
	*/
	public function __construct($id = null) {

		if(!is_null($id)) {

			$this->setId($id);
		}
	}

	/**
	* Set Message_ID
	*
	* @access public
	*
	* @param Message_ID
	*/
	public function setId($id) {

		$this->id = $id;
	}
	
	/**
	* Add CL-Command to Message
	* 
	* Set color command. Format is CL 0xAABBCCDDh where AA is number of led from 00 to 04, BB, CC and DD are color values for red, green and blue.
	*
	* @todo Not implemented yet
	* @tutorial http://www.cs.uta.fi/hci/spi/jnabserver/documentation/index.html#block_message
	*
	* @access public
	*/
	public function addColor() {

	}

	/**
	* Add PL-Command to Message
	* 
	* Set palette command. Format is PL 0X where X is from 0 to 7. This sets palette color for choreography files.
	* Throws MessageBlockException if value is not between 0 and 7.
	*
	* @tutorial http://www.cs.uta.fi/hci/spi/jnabserver/documentation/index.html#block_message
	*
	* @access public
	* @throws MessageBlockException
	*
	* @param Int between 0 and 7
	*/
	public function addPalette($x) {

		if(!in_array($x, range(0, 7))) {

			throw new exception\MessageBlockException('PL Command only accepts values between 0 and 7');
		}

		$this->data[] = 'PL 0' . $x;
	}

	/**
	* Add CH-Command for local File to Message
	* 
	* Choreography command. Format is CH <url>. This command gets choreography file from given url and plays it.
	* Throws MessageBlockException if File does not exist.
	*
	* @tutorial http://www.cs.uta.fi/hci/spi/jnabserver/documentation/index.html#block_message
	*
	* @access public
	* @throws MessageBlockException
	*
	* @param String Filename without .chor
	*/
	public function addLocalChor($name) {

		$realname = (substr($name, 0, 4) == 'tmp_' ? 'tmp' . DS . substr($name, 4) : $name);

		$path = 'chor' . DS . 'choreographies' . DS . $realname . '.chor';
		$file = PATH_FILES . DS . $path;

		if(!file_exists($file)) {

			throw new exception\MessageBlockException('Chorfile ' . $file . ' does not exist');
		}

		$this->data[] = 'CH broadcast' . DS . 'chor' . DS . $name . '.chor';
	}

	/**
	* Add CH-Command for remote File to Message
	* 
	* Choreography command. Format is CH <url>. This command gets choreography file from given url and plays it.
	*
	* @tutorial http://www.cs.uta.fi/hci/spi/jnabserver/documentation/index.html#block_message
	*
	* @access public
	*
	* @param String Url to .chor-File
	*/
	public function addChor($url) {

		$this->data[] = 'CH ' . $url;
	}

	/**
	* Add MU-Command to Message
	* 
	* Play sound command. Format is MU <url>. Plays sound from given url.
	*
	* @tutorial http://www.cs.uta.fi/hci/spi/jnabserver/documentation/index.html#block_message
	*
	* @access public
	*
	* @param String
	*/
	public function addSound($url) {

		$this->data[] = 'MU ' . $url;
	}

	/**
	* Add ST-Command to Message
	* 
	* Play stream command. Format is ST <url>. Plays shoutcast streaming radio from given URL.
	*
	* @tutorial http://www.cs.uta.fi/hci/spi/jnabserver/documentation/index.html#block_message
	*
	* @access public
	*
	* @param String
	*/
	public function addLocalStream($name) {

		$this->data[] = 'ST ' . $name;
	}

	/**
	* Add MW-Command to Message
	* 
	* Wait command. Doesn't take any parameters. This command forces Nabaztag to finish given commands (e.g. playing sound) before going to the next one.
	*
	* @tutorial http://www.cs.uta.fi/hci/spi/jnabserver/documentation/index.html#block_message
	*
	* @access public
	*/
	public function addWait() {

		$this->data[] = 'MW';
	}

	/**
	* Set ID for generated Message
	*
	* @access private
	**/
	private function prependId() {

		$this->data = array_merge(array('ID ' . $this->id), $this->data);
	}

	/**
	* Encode Message so that Nabaztag can understand
	* If param is null, $this->data is used.
	*
	* @access private
	*
	* @param mixed optional (String|Array)	
	*/
	private function encode($msg = null) {

		if(is_null($msg)) {

			$this->prependId();
			$msg = $this->data;
		}

		if(is_array($msg)) {

			$msg = join("\n", $msg);
		}

		if(substr($msg, -1) != "\n") $msg .= "\n";

		$binary = chr(1);
		$previousChar = 35;

		for($i=0;$i<strlen($msg);$i++) {

			$currentChar = ord($msg[$i]);
			$code = ($this->invtable[$previousChar % 128]*$currentChar+47) % 256;
			$previousChar = $currentChar;
			$binary .= chr($code);
		}

		$data = implode( '', unpack( 'H*', $binary));

		$tmp = str_split($data, 2);
		foreach($tmp as $key => $value) {

			$tmp[$key] = strtoupper($value);
		}

		$this->hex = $tmp;
	}

	/**
	* Returns an array with hexadecimal representation of the message
	* 
	* @access public
	*
	* @param String optional
	*/
	public function getHex($msg = null) {

		$this->encode($msg);

		return $this->hex;
	}
}
