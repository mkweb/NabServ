<?php
namespace nabserv\plugins;

use nabserv\Nabaztag;
use \base\Logger;

/**
* PingIntervalPlugin
*
* This Plugin changed the PingInterval of the Nabaztag
*
* @package nabserv.plugins
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class PingInterval extends Plugin {

	/**
	* Blocktype 0A for Message
	* @tutorial http://www.cs.uta.fi/hci/spi/jnabserver/documentation/index.html#blocks
	* @param String
	*/
	protected $blockType = '03';

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

		if($data < 1) {

			return false;
		}

		$seconds = sprintf('%02d', dechex($data));

		Nabaztag::getInstance(NAB_SERIAL)->setNewData('pinginterval', $seconds);
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

		$data = Nabaztag::getInstance(NAB_SERIAL)->getData('pinginterval');

		if(isset($data['seen']) && $data['seen'] == false) {

			if($data == null) {

				$interval = 5;
			} else {

				$interval = $data['data'];
			}

			$this->data = array($interval);
			Nabaztag::getInstance(NAB_SERIAL)->setSeen('pinginterval', true);
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
			'name' => 'Sekunden',
			'value' => '5'
		);
	}
}

?>
