<?php
namespace homepage;

use base\Lang;
use base\Logger;
use base\LookupTable;
use nabserv\Nabaztag;

/**
* class UserManager
*
* @package homepage
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class UserManager extends helpers\ValidateAble {

	/**
	* ValidationArray
	*
	* These ValidationRules must be conform to helpers\ValidateAble
	*
	* @access protected
	* @var Array
	*/
	protected $validate = array();

	/**
	* DataStore
	* @access protected
	* @var Array
	*/
	protected $data = array();

	/**
	* @access private
	* @var String
	*/
	private $username;

	/**
	* File with serialized UserData
	*
	* @access private
	* @var String
	*/
	private $file;

	private $lastError = null;

	/**
	* Constructor
	*
	* Loading Data
	*
	* @access public
	*
	* @param String
	*/
	public function __construct($username) {

		$this->validate = array(
			'username' => array(
				array(
					'regexp' => '/^([a-z0-9-_]*)$/i',
					'message' => Lang::get('validation.user.username.regexp', array('allowed' => 'A-Z, 0-9, -, _'))
				),
				array(
					'length' => '2-20',
					'message' => Lang::get('validation.user.username.length', array('from' => 2, 'to' => 20))
				),
				array(
					'func' => 'exists',
					'message' => Lang::get('validation.user.username.exists')
				)
			),
			'password' => array(
				'minlength' => 6,
				'message' => Lang::get('validation.user.password.minlen', array('length' => 6))
			)
		);

		$this->username = $username;
		$this->file = dirname(HOMEPAGE_BASE_PATH) . DS . 'database' . DS . 'user' . DS . substr($username, 0, 1) . DS . substr($username, 0, 2) . DS . strtolower($username) . DS . 'user.serialized';

		$this->setData('username', $username);

		if($this->exists()) {

			$this->load();
		}
	}

	/**
	* Returns true if Password mathes
	*
	* @access public
	*
	* @param String
	*
	* @return boolean
	*/
	public function login($password) {

		return $this->checkPassword($password);
	}

	/**
	* Returns true if user is registered
	*
	* User is registered when his serialized-File exists
	*
	* @access public
	*
	* @return boolean
	*/
	public function exists() {

		return file_exists($this->file);
	}

	/**
	* Hashes Password and creates serialized file
	*
	* @access public
	*/
	public function register() {

		$this->hashPassword();

		$this->createFile($this->file);
		$this->save();
	}

	/**
	* Created a token for given Nabaztag and connects user to it
	*
	* Name and Token will be stored in Nabaztag
	* Serialnumber will be stored in User
	*
	* @access public
	*
	* @param String
	* @param String
	*
	* @return boolean
	*/
	public function createNabaztag($serial, $name) {

		$current = $this->getData('rabits');

		if(is_array($current) && in_array($serial, $current)) {

			return false;
		}

		$nabaztag = Nabaztag::getInstance($serial);

		if($nabaztag->getConfig('name')) {

			return false;
		}

		if(!$nabaztag->getConfig('token')) {

			$nabaztag->setConfig('token', strtoupper(substr(md5(microtime()), 0, 6)));
		}
		
		$nabaztag->setConfig('name', $name);

		if(is_null($current)) $current = array();

		$current[$serial] = $serial;

		$this->setData('rabits', $current);
		$this->save();

		$lookup = LookupTable::getInstance('nabaztag');
		$lookup->add(array('serial' => $serial, 'name' => $name, 'username' => $this->username));

		return true;
	}

	public function updateNabaztagConfig($serial, $config) {

		$lookup = array('serial', 'name', 'username');

		$nabaztag = Nabaztag::getInstance($serial);
		$table = LookupTable::getInstance('nabaztag');

		foreach($config as $key => $value) {

			if($key == 'name') {

				$search = $table->find(array('serial' => $serial));

				if(!is_array($search) || !isset($search['name'])) {

					Logger::warn('Trying to change not existing Config Username for Nabaztag ' . $serial);
					$this->setError(Lang::get('error.default'));

					return false;
				} else {

					if($search['name'] == $config['name']) {

						$this->setError(Lang::get('validation.rabit.rename.thesame'));

						return false;
					}
				}

				$check = $table->find(array('name' => $config['name']));
				
				if(is_array($check) && count($check) > 0) {

					$this->setError(Lang::get('validation.rabit.rename.exists', array('name' => $config['name'])));

					return false;
				}
			}

			$changeLookup = (in_array($key, $lookup) ? true : false);

			if(in_array($key, $lookup)) {

				$table->modify(array('serial' => $serial), array($key => $value));
			}

			$nabaztag->setConfig($key, $value);
		}

		return true;
	}

	/**
	* Remove Name from Nabaztag and Nabaztags serial from User
	*
	* @access public
	*
	* @param String
	*
	* @return boolean
	*/
	public function removeNabaztag($serial) {

		$current = $this->getData('rabits');

		foreach($current as $key => $value) {

			if($value == $serial) {

				unset($current[$key]);

				$nabaztag = new Nabaztag($serial);

				$nabaztag->removeConfig('name');
				$nabaztag->removeConfig('apps');
				$nabaztag->removeConfig('lastcron');

				$this->setData('rabits', $current);
				$this->save();

				$table = LookupTable::getInstance('nabaztag');
				$table->remove(array('serial' => $serial));

				return true;
			}
		}

		return false;
	}

	/**
	* Returns true if User has added at least one Nabaztag
	*
	* @access public
	*
	* @return boolean
	*/
	public function hasRabits() {

		$rabits = $this->getData('rabits');

		if(!$rabits || (is_array($rabits) && count($rabits) < 1)) {

			return false;
		}

		return true;
	}

	/**
	* Returns Array of following Data for every registered Nabaztag by this user
	*
	* <ul>
	*   <li>serial</li>
	*   <li>name</li>
	*   <li>token</li>
	*   <li>lastseen</li>
	* </ul>
	*
	* @access public
	*
	* @return Array
	*/
	public function getRabits() {

		$tmp = $this->getData('rabits');
		$rabits = array();

		if(count($tmp) > 0) {
			foreach($tmp as $serial) {

				$nabaztag = new Nabaztag($serial);

				$rabits[$serial]['serial'] = $serial;
				$rabits[$serial]['name'] = $nabaztag->getConfig('name');
				$rabits[$serial]['token'] = $nabaztag->getConfig('token');
				$rabits[$serial]['lastseen'] = $nabaztag->getConfig('lastseen');
			}
		}

		return $rabits;
	}

	/**
	* Generates Hash from given Password and check it through saved Password
	*
	* @access private
	*
	* @param String
	*
	* @return boolean
	*/
	private function checkPassword($password) {

		$right = $this->getData('password');

		$pwhash = md5($password);
		$salt = substr($right, 0, 5);

		$checkhash = $salt . substr(md5($salt . $pwhash), 5);

		return ($checkhash == $right);
	}

	/**
	* Generates Hash from saved Password and overrides it
	*
	* @access private
	*/
	private function hashPassword() {

		$tmp = md5(microtime());
		$pwhash = md5($this->getData('password'));

		$salt = substr($tmp, 0, 5);
		$pwhash = $salt . substr(md5($salt . $pwhash), 5);

		$this->setData('password', $pwhash);
	}

	/**
	* Stores data 
	*
	* @access public
	* 
	* @param String
	* @param mixed
	*/
	public function setData($key, $value) {

		$this->data[$key] = $value;
	}
	
	/**
	* Returns saved value if exists
	*
	* @access public
	*
	* @param String
	*
	* @return mixed (mixed|null)
	*/
	public function getData($key) {

		return (isset($this->data[$key]) ? $this->data[$key] : null);
	}

	/**
	* Removes saved value if exists
	*
	* @access public
	*
	* @param String
	*
	* @return boolean
	*/
	public function removeData($key) {

		if(isset($this->data[$key])) {

			unset($this->data[$key]);
			return true;
		}

		return false;
	}

	/**
	* Creates directory and file if not exists
	*
	* @access private
	*
	* @return Boolean
	*/
	private function createFile($path) {

		$file = substr($path, strlen(dirname(HOMEPAGE_BASE_PATH)));

		$tmp = explode("/", $file);
		if($tmp[0] == '') array_shift($tmp);

		$fileName = array_pop($tmp);

		$currentPath = dirname(HOMEPAGE_BASE_PATH);
		foreach($tmp as $dir) {

			$currentPath .= '/' . $dir;
			if(!file_exists($currentPath)) {

				mkdir($currentPath, 0777);
			}
		}

		touch($path);
		chmod($path, 0777);

		return true;
	}

	/**
	* Loading Data from serialized file
	*
	* @access public
	*/
	public function load() {

		$this->data = unserialize(file_get_contents($this->file));
	}

	/**
	* Saving Data in serialized file
	*
	* @access public
	*/
	public function save() {

		$data = serialize($this->data);

		$fh = fopen($this->file, 'w');
		fputs($fh, $data);
		fclose($fh);
	}

	/**
	* Method for Validation-Rule
	*
	* This Method is triggered by helpers\ValidateAble::validate()
	*
	* @access protected
	*
	* @return boolean
	*/
	protected function validate_Exists($username) {

		return !$this->exists($username);
	}

	public function getLastError() {

		return $this->lastError;
	}

	private function setError($msg) {

		$this->lastError = $msg;
	}
}

?>
