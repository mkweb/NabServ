<?php
namespace base;

use \homepage\Request;

/**
* Class Page
*
* Parent-Class for every MVC-Page
*
* @package base
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
abstract class Page {

	/**
	* Data which will be assigned to Template
	* @access private
	* @var Array
	*/
	private $data = array();

	/**
	* @access private
	* @var String
	*/
	private $title;

	/**
	* @access private
	* @var String
	*/
	private $content;
	
	/**
	* If this Var is not equal to null, Browser will be redirected to "/?page=[$redirect]"
	* @access private
	* @var String
	*/
	private $redirect;

	/**
	* If this var equals true and $_SESSION['loggedin'] is not set, error.php with an error-message is displayed
	* @access protected
	* @var boolean
	*/
	protected $protected = false;

	/**
	* Path to view-File
	* @access protected
	* @var String
	*/
	protected $view;

	/**
	* @access protected
	* @var homepage\Request
	*/
	protected $request;

	/**
	* Constructor
	*
	* Preparing View and checking Protection
	*
	* @access public
	*
	* @param homepage\Request
	*/
	public function __construct(Request $request) {

		$this->request = $request;

		$tmp = explode("\\", get_class($this));
		$name = array_pop($tmp);

		$this->view = PATH_TEMPLATES . DS . TARGET_NAMESPACE . DS . lcfirst($name) . '.php';

		if($this->protected && !isset($_SESSION['loggedin'])) {

			$this->view = PATH_TEMPLATES . DS . TARGET_NAMESPACE . DS . 'error.php';
			$this->set('message', 'Du musst eingeloggt sein, um diese Seite benutzen zu können');
		}
	}

	/**
	* Method for Page-Logic
	*
	* @abstract
	*/
	abstract function process();

	/**
	* Replaces view with Error-View and assignes message
	*
	* @access public
	*
	* @param String
	*/
	public function setGlobalError($message) {

		$this->view = PATH_TEMPLATES . DS . TARGET_NAMESPACE . DS . 'error.php';
		$this->set('message', $message);
	}

	/** 
	* Getter for Page-Title
	*
	* @access public
	* 
	* @return String
	*/
	public function getTitle() {

		return $this->title;
	}

	/** 
	* Preparing Redirect
	*
	* @access public
	* 
	* @param String
	*/
	public function setRedirect($code) {

		$this->redirect = $code;
	}

	/** 
	* Returns prepared Redirect
	*
	* @access public
	* 
	* @return mixed [String|null]
	*/
	public function getRedirect() {

		return $this->redirect;
	}

	/** 
	* Setting Vars
	*
	* This vars will be assigned to Template
	*
	* @access public
	* 
	* @param String
	* @param mixed
	*/
	public function set($key, $value) {

		$this->data[$key] = $value;
	}

	/** 
	* Setter for ErrorFlash
	*
	* The Flash is stored in Session and will be displayed on next Pageload
	*
	* @access public
	* 
	* @param String
	*/
	public function setErrorFlash($error) {

		$_SESSION['errors'][] = $error;
	}

	/** 
	* Setter for InformationFlash
	*
	* The Flash is stored in Session and will be displayed on next Pageload
	*
	* @access public
	* 
	* @param String
	*/
	public function setFlash($msg) {

		$_SESSION['flash'][] = $msg;
	}

	/**
	* Getter for whole Data Array
	*
	* @access public
	*
	* @return Array
	*/
	public function getData() {

		return $this->data;
	}

	/**
	* Merge given Data to current
	*
	* @access public
	*
	* @param Array
	*/
	public function addData(Array $data) {

		$this->data = array_merge($this->data, $data);
	}

	/** 
	* Execute View
	*
	* This Method asigns prepares Vars for view and executes it.
	*
	* @access public
	* 
	* @return String
	*/
	public function getContent() {

		if(file_exists($this->view)) {

			extract($this->data);
			
			$request = $this->request;

			ob_start();
			require_once($this->view);
			$this->content = ob_get_clean();
		}

		return $this->content;
	}
}

?>
