<?php
namespace nabserv;

/**
* Helperclass for Nabaztag-Request
*
* @package nabserv
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Request {

	/**
	* Headers from getallheaders()
	* @param Array
	*/
	private $headers;

	/**
	* GET-Vars
	* @param Array
	*/
	private $data = array();

	/**
	* Constructor
	*
	* Preparing headers and GET-Vars
	*
	* @access public
	*/
	public function __construct() {

		$this->data = $_GET;
		$this->headers = getallheaders();
	}

	/**
	* Checking useragent-header for MTL
	*
	* @access public
	*
	* @return Boolean
	*/
	public function isValid() {

		$userAgent = $this->getHeader('User-Agent');

		if($userAgent == 'MTL') {

			return true;
		}

		return false;
	}

	/**
	* Returns true if Nabaztag send sd-var and var > 0
	*
	* @access public
	*
	* @return Boolean
	*/
	public function hasAction() {

		return ($this->getData('sd') > 0);
	}

	/**
	* Returns sd-var from Nabaztag
	*
	* @access public
	*
	* @return String
	*/
	public function getAction() {

		return $this->getData('sd');
	}

	/**
	* Returns all Requestheaders
	*
	* @access public
	*
	* @return Array
	*/
	public function getAllHeader() {

		return $this->headers;
	}

	/**
	* Returns Requestheader for key or null
	*
	* @access public
	*
	* @param String
	*
	* @return mixed (String|null)
	*/
	public function getHeader($key) {

		return (array_key_exists($key, $this->headers) ? $this->headers[$key] : null);
	}

	/**
	* Returns all GET-Vars
	*
	* @access public
	*
	* @return Array
	*/
	public function getAllData() {

		$return = $this->data;

		if(isset($return['request'])) {

			unset($return['request']);
		}

		return $return;
	}

	/**
	* Return GET-Var by key or null
	*
	* @access public
	*
	* @param String
	*
	* @return mixed (String|null)
	*/
	public function getData($key) {

		return (array_key_exists($key, $this->data) ? $this->data[$key] : null);
	}
}

?>
