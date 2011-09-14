<?php
namespace nabserv;

/**
* Class Ping
*
* Handler for ping-request by Nabaztag
*
* @package nabserv
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Ping {

	/**
	* Request Nabaztag sent
	* @param Request
	*/
	private $request;

	/**
	* Response to Nabaztag
	* @param Response
	*/
	private $response;

	/**
	* PluginStore
	* @param Array
	*/
	private $plugins = array();

	/**
	* Constructor
	*
	* Prepares Request and Response
	*
	* @access public
	*
	* @param Request
	*/
	public function __construct(Request $request) {

		$this->request = $request;
		$this->response = new Response();
	}

	/**
	* Loading all Plugins in plugin-direction and sets request
	*
	* @access public
	*/
	public function loadPlugins() {

		$files = glob(dirname(__FILE__) . '/plugins/*');
		
		foreach($files as $file) {

			$className = ucfirst(substr($file, strrpos($file, '/') + 1, -4));

			if($className != 'Plugin') {

				$className = "nabserv\\plugins\\" . $className;
				$plugin = new $className;
				$plugin->setRequest($this->request);

				$this->plugins[] = $plugin;
			}
		}
	}

	/**
	* Looping through loaded Plugins and tries to execute $plugins->ping()
	* If successed plugin is attached to response
	*
	* @access public
	*/
	public function handle() {

		foreach($this->plugins as $key => $plugin) {

			if($plugin->ping()) {

				$this->response->addPlugin($plugin);
			}
		}
	}

	/**
	* Prints full generated response for Nabaztag
	*
	* @access public
	*/
	public function sendResponse() {

		echo $this->response;
	}
}

?>
