<?php
namespace nabserv\plugins;

use nabserv\Nabaztag;
use nabserv\MessageBlock;
use \base\Logger;

/**
* StreamPlugin
*
* This Plugins forces Nabaztag to play a stream
*
* @package nabserv.plugins
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Stream extends Plugin {

	/**
	* Blocktype 0A for Message
	* @tutorial http://www.cs.uta.fi/hci/spi/jnabserver/documentation/index.html#blocks
	* @param String
	*/
	protected $blockType = '0A';

	/**
	* After this time the Plugin is played randomly
	* @param Int
	*/
	protected $randomTime = 1200;

	/**
	* Called if Api has Requested this Plugin
	* Returns true if data is valid
	*
	* @access public
	*
	* @param String
	*
	* @return Boolean
	*/
	public function api($data) {

		Nabaztag::getInstance(NAB_SERIAL)->setNewData('stream', $data);
		return true;
	}

	/**
	* Called by ping-request from Nabaztag
	* Returns true if there is something to do
	*
	* @access public
	* 
	* @return Boolean
	*/
	public function ping() {

		$data = Nabaztag::getInstance(NAB_SERIAL)->getNewData();

		if(isset($data['stream'])) {

			$dir = API_BASE_PATH . '/files/mood/';
			$files = glob($dir . "/*");

			$mp3 = basename($files[array_rand($files)]);

			$mp = MessageBlock::getInstance(rand(11111, 99999));
			$mp->addLocalStream($data['stream']);
			$mp->addWait();
			$this->data = $mp->getHex();

			Nabaztag::getInstance(NAB_SERIAL)->setSeen('stream', true);
			return true;
		}

		return false;
	}

	/**
	* This method gives demo the information which fields are used for this Plugin
	* The return can be 1 or 2-dimensional
	*
	* If 2-dimensional the fields must include an id
	*
	* Possible Types are:
	* <ul>
	*     <li>type - [text,textare,dropdown]</li>
	*     <li>id - for more than one field</li>
	*     <li>name - legend for input on demopage</li>
	*     <li>value - preselected defaultvalue</li>
	* </ul>
	*
	* @access public
	*
	* @return Array
	*/
	public function demo() {

		return array(
			'type' => 'text',
			'name' => 'URL',
			'value' => BASE_URL . '/vl/test.mp3'
		);
	}
}

?>
