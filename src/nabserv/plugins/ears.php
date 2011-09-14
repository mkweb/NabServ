<?php
namespace nabserv\plugins;

use nabserv\Nabaztag;
use nabserv\Choreographie;
use nabserv\MessageBlock;
use \base\Logger;

/**
* EarsPlugin
*
* This Plugin an short chor to move ears for Nabaztag by given data from API
*
* @package nabserv.plugins
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Ears extends Plugin {

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

		if(strlen($data) < 1) {

			return false;
		}

		list($right, $left) = explode(",", $data);
		$data = array('right' => $right, 'left' => $left);

		Nabaztag::getInstance(NAB_SERIAL)->setNewData('earsapi', $data);

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

		if(isset($data['earsapi'])) {

			$data = $data['earsapi'];

			$left = $data['left'];
			$right = $data['right'];

			$name = 'tmp/' . NAB_SERIAL . '_' . md5(microtime());

			$ch = new Choreographie(100);

			$ch->addEar(0, $right, 0);
			$ch->addEar(1, $left, 0);

			$ch->save($name);

			$name = str_replace("tmp/", "tmp_", $name);

			$mb = MessageBlock::getInstance(rand(11111, 99999));
			$mb->addLocalChor($name);
			$this->data = $mb->getHex();

			Nabaztag::getInstance(NAB_SERIAL)->setSeen('earsapi', true);
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
	public function handleDemo($d) {

		return $d['right'] . "," . $d['left'];
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
				'name' => 'left',
				'id' => 'left',
				'value' => $values
			),
			array(
				'type' => 'dropdown',
				'name' => 'right',
				'id' => 'right',
				'value' => $values
			)
		);
	}
}

?>
