<?php
namespace base;

/**
* Base Controller
*
* @package base 
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Controller {

	/**
	* Mapping request to target namespace
	* @var Array
	*/
	private $typeMapping = array(
		'vl' => 'nabserv',
		'web' => 'homepage'
	);

	/**
	* ClientRequest
	* @var Array
	*/
	protected $request = array();

	/**
	* GET-Vars
	* @var Array
	*/
	protected $data = array();

	/**
	* Setter for ClientRequest
	* 
	* @access public
	* 
	* @pram Array
	*/
	public function setRequest(Array $request = array(), Array $data = array()) {

		$this->request = $request;
		$this->data = $data;
	}

	/**
	* Handling Global actions
	* This Method is triggering Base-Controller for each Project
	*
	* @access public
	*/
	public function handle() {

		$type = array_shift($this->request);
		if($type == '') {

			$type = 'web';
		}

		if(isset($this->typeMapping[$type])) {

			$type = $this->typeMapping[$type];
		}

		define('TARGET_NAMESPACE', $type);

		$baseConst = strtoupper($type) . '_BASE_PATH';
		if(!defined($baseConst)) {

			define($baseConst, PATH_ROOT . DS . $type);
		}

		$controllerName = $type . "\\Controller";

		$controller = new $controllerName;
		$controller->setRequest($this->request, $this->data);
		$controller->handle();
	}
}

?>
