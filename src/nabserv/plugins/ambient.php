<?php
namespace nabserv\plugins;

use nabserv\Nabaztag;
use nabserv\Choreographie;
use \base\Logger;

/**
* AmbientPlugin
*
* This Plugin an ambient for Nabaztag by given data from API
*
* @todo does not work atm
*
* @package nabserv.plugins
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Ambient extends Plugin {

	/**
	* Blocktype 0A for Message
	* @tutorial http://www.cs.uta.fi/hci/spi/jnabserver/documentation/index.html#blocks
	* @param String
	*/
	protected $blockType = '04';

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

		$data = explode(",", $data);

		if(!is_array($data) || count($data) != 3) {

			return false;
		}

		Nabaztag::getInstance(NAB_SERIAL)->setNewData('ambient', $data);
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

		if(isset($data['ambient'])) {

			$data = $data['ambient'];

			list($nose, $right, $left) = $data;

			$length = 22 + $nose;

			for($i = 0; $i < $length; $i++) {

				$this->data[] = '00';
			}

			$this->data[0] = '7F';
			$this->data[1] = 'FF';
			$this->data[2] = 'FF';
			$this->data[3] = 'FF';

			if($right > 0) {
				$this->data[20] = sprintf("%02s", strtoupper(dechex($right)));
			}
			if($left > 0) {
				$this->data[21] = sprintf("%02s", strtoupper(dechex($left)));
			}

			Logger::debug(print_r($this->data, true));

			Nabaztag::getInstance(NAB_SERIAL)->setSeen('ambient', true);
			return true;
		}

		return false;
	}

	/**
	* If demo() returns more then one input, this method is used from demo-interface
	* to transform array to request-string
	*
	* @access public
	*
	* @param Array
	*
	* @return String
	*/
	public function handleDemo($data) {

		return sprintf("%s,%s,%s", $data['nose'], $data['right'], $data['left']);
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

		$values = array();
		for($i = 0; $i <= 17; $i++) {

			$values[$i] = $i;
		}

		return array(
			array(
				'type' => 'dropdown',
				'name' => 'Nose',
				'id' => 'nose',
				'value' => array('0' => 'nothing', '1' => 'once', '2' => 'twice')
			),
			array(
				'type' => 'dropdown',
				'name' => 'Ear right',
				'id' => 'right',
				'value' => $values
			),
			array(
				'type' => 'dropdown',
				'name' => 'Ear left',
				'id' => 'left',
				'value' => $values
			),
		);
	}
}

?>
