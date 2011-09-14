<?php
namespace nabserv\plugins;

use nabserv\Nabaztag;
use nabserv\Choreographie;
use nabserv\MessageBlock;
use \base\Logger;

/**
* ChorPlugin
*
* This Plugin generates an choreographie for Nabaztag by given data from API
*
* @package nabserv.plugins
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Chor extends Plugin {

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

		$data = explode(",", $data);
		$tempo = array_shift($data) * 10;

		$name = 'tmp/' . NAB_SERIAL . '_' . md5(microtime());

		$ch = new Choreographie($tempo);

		$timeline = array();
		while($this->readBlock($data, $timeline));

		$lasttime = 0;
		foreach($timeline as $time => $blocks) {

			$timediff = $time - $lasttime;
			$lasttime = $time;

			$first = true;
			foreach($blocks as $block) {

				$tmp = array_keys($block);
				$type = $tmp[0];

				$time = 0;
				if($first) {

					$time = $timediff;
					$first = false;
				}

				switch($type) {

					case 'led':
						$block = $block['led'];
						$ch->addLed($block['led'], $block['color'], $time);
						break;

					case 'ear':
						$block = $block['ear'];
						$ch->addEar($block['site'], $block['pos'], $block['dir'], $time);
						break;
				}
			}
		}

		$ch->save($name);

		Nabaztag::getInstance(NAB_SERIAL)->setNewData('chor', $name);
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

		if(isset($data['chor'])) {

			$name = $data['chor'];

			$name = str_replace("tmp/", "tmp_", $name);

			$mb = new MessageBlock(rand(11111, 99999));
			$mb->addLocalChor($name);
			$this->data = $mb->getHex();

			Nabaztag::getInstance(NAB_SERIAL)->setSeen('chor', true);
			return true;
		}

		return false;
	}

	/**
	* Reading next Block from API-Data
	*
	* @access private
	*
	* @param Array
	* @param Array
	*
	* @return Boolean
	*/	
	private function readBlock(&$data, &$timeline) {

		$return = false;

		if(!isset($data[1])) {

			return false;
		}

		$type = $data[1];

		switch($type) {

			case 'motor':

				$d = $this->slice($data, 6);

				$site = $d[2];
				$pos = ($d[3] / 10);
				$dir = $d[5];
				$time = $d[0];

				$timeline[$time][] = array(
					'ear' => array(
						'site' => $site,
						'pos' => $pos,
						'dir' => $dir
					)
				);
				$return = true;
				break;

			case 'led':

				$d = $this->slice($data, 6);

				$time = array_shift($d);
				array_shift($d);		// keyword "led"
				$led = array_shift($d);

				$color = '';
				foreach($d as $char) {

					$color .= sprintf("%02s", strtoupper(dechex($char)));
				}
				$timeline[$time][] = array(
					'led' => array(
						'led' => $led,
						'color' => $color
					)
				);
				$return = true;
				break;
		}

		return $return;
	}

	/**
	* Real slice (including removal from source) from Array beginning 
	*
	* @access private
	* 
	* @param Array
	* @param Int
	*
	* @return Array
	*/
	private function slice(&$arr, $size) {

		$return = array_slice($arr, 0, $size);

		for($i = 0; $i < $size; $i++) {

			array_shift($arr);
		}

		return $return;
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
			'name' => 'Chor',
			'value' => "10,0,motor,1,20,0,0,0,led,2,0,238,0,2,led,1,250,0,0,3,led,2,0,0,0"
		);
	}
}

?>
