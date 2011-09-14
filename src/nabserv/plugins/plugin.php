<?php
namespace nabserv\plugins;

use \base\Logger;

use nabserv\Request;
use nabserv\Nabaztag;

/**
* AbstractPlugin
*
* This Abstract class is the Core-Class for all Plugins
*
* @package nabserv.plugins
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
abstract class Plugin {

	/**
	* Blocktype 0A for Message
	* @tutorial http://www.cs.uta.fi/hci/spi/jnabserver/documentation/index.html#blocks
	* @param String
	*/
	protected $blockType;

	/**
	* Array with hexadecimal representation of plugindata
	* @var Array
	*/
	protected $data = array();

	/**
	* Request from Nabaztag
	* @var Request
	*/
	protected $request;

	/**
	* Used for playByRandom - after this time in seconds plugin is executed randomly
	* @var Int
	*/
	protected $randomTime = 0;

	/**
	* Setter for Requestobject
	*
	* @access public
	*
	* @param Request
	*/
	public function setRequest(Request $request) {
		Logger::debug(__METHOD__, 'plugin');

		$this->request = $request;
	}

	/**
	* Returns array of hexadecimal representation of plugindata
	*
	* @access public
	*
	* @return Array
	*/
	public function getData() {
		Logger::debug(__METHOD__, 'plugin');

		return array_merge(array($this->blockType), array('00', '00', dechex(count($this->data))), $this->data);
	}

	/**
	* If time from last execute is grater than $this->randomTime, execute randomly
	*
	* @access protected
	* 
	* @param String
	*
	* @return Boolean
	*/
	protected function playByRandom($type) {

		$last = Nabaztag::getInstance(NAB_SERIAL)->getData($type);

		if(!is_null($last) && isset($last['last'])) {

			$lastPlayed = $last['last'];

			if((time() - $lastPlayed) > $this->randomTime) {

				return (rand(0, 100) == 0 ? true : false);
			}
		}
	}

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
	abstract function api($data);
	
	/**
	* Called by ping-request from Nabaztag
	* Returns true if there is something to do
	*
	* @access public
	* 
	* @return Boolean
	*/
	abstract function ping();
}

?>
