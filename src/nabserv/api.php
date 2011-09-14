<?php
namespace nabserv;

use \nabserv;
use \base\Logger;

use nabserv\Request;
use nabserv\Nabaztag;

/**
* Class API
* 
* Handles API-Calls
*
* @package nabserv
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Api {

	/**
	* Request API sent
	* @param Request
	*/
	private $request;

	/**
	* PluginStore
	* @param Array
	*/
	private $plugins = array();

	/**
	* Data sent from API
	* @param Array
	*/
	private $data = array();

	/**
	* Constructor
	*
	* Prepares Request
	*
	* @access public
	*
	* @param Request
	*/
	public function __construct(Request $request) {
		Logger::debug(__METHOD__, 'api');

		$this->request = $request;
	}

	/**
	* Loading all Plugins in plugin-direction
	*
	* @access public
	*/
	public function loadPlugins() {
		Logger::debug(__METHOD__, 'api');

		$data = $this->request->getAllData();

		unset($data['sn']);
		unset($data['token']);

		$this->data = $data;

		foreach($this->data as $pluginName => $data) {

			if(!$this->loadPlugin($pluginName)) {

				return $pluginName;
			}
		}

		return null;
	}

	/**
	* Loading Plugin and sets request
	*
	* @access public
	*
	* @param String
	*
	* @return Boolean
	*/
	public function loadPlugin($pluginName) {
		Logger::debug(__METHOD__, 'api');

		$name = $pluginName;
		$className = ucfirst($pluginName);

		if($className != 'Plugin') {

			$className = "nabserv\\plugins\\" . $className;
			$plugin = new $className;
			$plugin->setRequest($this->request);
		}

		Logger::debug("API - Found Plugin " . get_class($plugin));

		if(is_null($plugin)) {

			return false;
		}

		$this->plugins[$name] = $plugin;
		return true;
	}

	/**
	* Looping through loaded Plugins and tries to execute $plugins->api()
	* Only returns true if every plugin is executed successfully
	*
	* @access public
	*
	* @return Boolean
	*/
	public function handle() {
		Logger::debug(__METHOD__, 'api');

		$return = true;
		foreach($this->plugins as $name => $plugin) {

			$data = $this->data[$name];

			if(!$plugin->api($data)) {

				$return = false;
			}
		}

		return $return;
	}
}

?>
