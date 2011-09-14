<?php
namespace homepage;

/**
* Simple Request-Wrapper
*
* @package homepage
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Request {

	const GET 	= 0;
	const POST 	= 1;

	/**
	* $_SERVER - Array
	*
	* @access private
	* @var Array
	*/	
	private $server;

	/**
	* $_GET - Array
	*
	* @access private
	* @var Array
	*/	
	private $get;

	/**
	* $_POST - Array
	*
	* @access private
	* @var Array
	*/	
	private $post;

	/**
	* Constructor
	*
	* @access public
	*/
	public function __construct() {

		$this->server 	= $_SERVER;
		$this->get 	= $_GET;
		$this->post 	= $_POST;
	}

	/**
	* Returns true if $_SERVER['REQUEST_METHOD'] equals 'POST'
	*
	* @access public
	*
	* @return boolean
	*/
	public function isPost() {

		if($this->server['REQUEST_METHOD'] == 'POST') {

			return true;
		}

		return false;
	}

	/**
	* Returns true if $_SERVER['REQUEST_METHOD'] equals 'GET'
	*
	* @access public
	*
	* @return boolean
	*/
	public function isGet() {

		if($this->server['REQUEST_METHOD'] == 'GET') {

			return true;
		}

		return false;
	}

	/**
	* Returnes Request-Value if set
	*
	* Type is either homepage\Request::POST or homepage\Request::GET
	*
	* @access public
	*
	* @param String optional default = homepage\Request::GET
	* @param Int
	*
	* @return mixed [String|null]
	*/
	public function get($key, $type = self::GET) {

		$pool = ($type == self::GET ? $this->get : $this->post);
		
		return (isset($pool[$key]) ? $pool[$key] : null);
	}
}

?>
