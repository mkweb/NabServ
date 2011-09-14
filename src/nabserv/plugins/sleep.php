<?php
namespace nabserv\plugins;

use nabserv\Nabaztag;
use nabserv\MessageBlock;
use \base\Logger;

/**
* SleepPlugin
*
* This Plugins forces Nabaztag to sleep or wakeup if current status is different
*
* @package nabserv.plugins
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Sleep extends Plugin {

	const MESSAGE_ID_ASLEEP = 2147483646;
	const MESSAGE_ID_WAKEUP = 2147483647;

	/**
	* Blocktype 0A for Message
	* @tutorial http://www.cs.uta.fi/hci/spi/jnabserver/documentation/index.html#blocks
	* @param String
	*/
	protected $blockType = '0A';

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

		if(!in_array($data, array('true', 'false', 0, 1))) {

			return false;
		}

		if($data == 'true') $data = true;
		if($data == 'false') $data = false;

		Nabaztag::getInstance(NAB_SERIAL)->setNewData('sleep', $data);

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

		if(isset($data['sleep'])) {

			$current = null;

			$req = $this->request->getData('tc');

			if($req == '7ffffffe') $current = true;
			if($req == '7fffffff') $current = false;

			if(($current === true && $data['sleep'] == true) || ($current === false && $data['sleep'] == false)) {

				Nabaztag::getInstance(NAB_SERIAL)->setSeen('sleep', true);
				return false;
			}

			if($data['sleep'] == true) {

				$this->data = MessageBlock::getInstance(self::MESSAGE_ID_ASLEEP)->getHex();
			} else {

				$this->data = MessageBlock::getInstance(self::MESSAGE_ID_WAKEUP)->getHex();
			}

			Nabaztag::getInstance(NAB_SERIAL)->setSeen('sleep', true);
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
			'type' => 'dropdown',
			'name' => 'Sleep',
			'value' => array('1' => 'true', '0' => 'false')
		);
	}
}

?>
