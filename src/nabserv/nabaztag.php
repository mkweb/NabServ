<?php
namespace nabserv;

use \base\Logger;

/**
* Class Nabaztag
*
* Handling relevant data for current Nabaztag
* Data is saved serialized in cache-file
*
* @package nabserv
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Nabaztag {

	/**
	* Random Hash to identify this Object
	* @param String
	*/
	private $uid;

	/**
	* Serialnumber
	* @param String
	*/
	private $serial;

	/**
	* Path to cache with all relevant for this Nabaztag
	* @param String
	*/
	private $file;

	/**
	* Path to cache with all configurations for this Nabaztag
	* @param String
	*/
	private $configFile;

	/**
	* All relevant data for this Nabaztag
	* @param Array
	*/
	private $data = array();

	/**
	* All configurations for this Nabaztag
	* @param Array
	*/
	private $configuration = array();

	/**
	* This var tells destructor if config should be saved
	* @param Boolean
	*/
	private $configurationChanged = false;

	/**
	* Singleton-Instances
	* @param Array
	*/
	private static $instances = array();

	/**
	* Singleton
	*
	* @static
	* @access public
	*
	* @param Int
	*
	* @return Nabaztag
	*/
	public static function getInstance($serial) {

		if(!isset(self::$instances[$serial])) {

			self::$instances[$serial] = new Nabaztag($serial);
		}

		return self::$instances[$serial];
	}

	/**
	* Constructor
	* 
	* Prepare data-cache and starts loading saved data
	*
	* @access public
	*
	* @param String
	*/
	public function __construct($serial) {

		$this->uid = md5(microtime());
		Logger::debug($this->uid . ' - ' . __METHOD__, 'nabaztag');

		$this->serial = $serial;

		$this->file 		= PATH_DB . DS . 'nabaztag' . DS . join(DS, str_split($this->serial, 2)) . DS . $this->serial . DS . 'nabaztag.serialized';
		$this->configFile 	= PATH_DB . DS . 'nabaztag' . DS . join(DS, str_split($this->serial, 2)) . DS . $this->serial . DS . 'configuration.serialized';
		
		$this->createFileIfNotExists($this->file);
		$this->createFileIfNotExists($this->configFile);

		$this->load();
		$this->loadConfiguration();
	}

	/**
	* Loads saved data from cache
	*
	* @access public
	*/
	public function load() {
		Logger::debug($this->uid . ' - ' . __METHOD__, 'nabaztag');

		$this->data = array();

		if(file_exists($this->file)) {

			$this->data = unserialize(file_get_contents($this->file));
		}

		if(!isset($this->data['ears'])) {

			$this->data['ears'] = array();

			$this->data['ears']['left'] = 0;
			$this->data['ears']['right'] = 0;
		}
	}

	/**
	* Loads configuration
	*
	* @access public
	*/
	public function loadConfiguration() {
		Logger::debug($this->uid . ' - ' . __METHOD__, 'nabaztag');

		$this->configuration = unserialize(file_get_contents($this->configFile));
	}

	/**
	* Creates directory and cache-file if not exists
	*
	* @access private
	*
	* @return Boolean
	*/
	private function createFileIfNotExists($path) {

		if(file_exists($path)) {

			return false;
		}

		$file = substr($path, strlen(PATH_ROOT));

		$tmp = explode("/", $file);
		if($tmp[0] == '') array_shift($tmp);

		$fileName = array_pop($tmp);

		$currentPath = PATH_ROOT;
		foreach($tmp as $dir) {

			$currentPath .= '/' . $dir;
			if(!file_exists($currentPath)) {

				mkdir($currentPath, 0777);
			} else {
			}
		}

		touch($path);
		chmod($path, 0777);

		return true;
	}

	/**
	* Returns data by key
	*
	* @access public
	*
	* @param String
	*
	* @return mixed (String|Array)
	*/
	public function setData($key, $data) {
		Logger::debug($this->uid . ' - ' . __METHOD__, 'nabaztag');

		$this->load();
		$this->data[$key] = $data;
		$this->save();
	}

	/**
	* Set data by key - seen = false is appended, so that plugins can read this
	*
	* @access public
	* 
	* @param String
	* @param mixed (String|Array)
	*/
	public function setNewData($key, $data) {
		Logger::debug($this->uid . ' - ' . __METHOD__, 'nabaztag');

		$this->load();
		Logger::debug(__METHOD__ . ' loaded data: ' . print_r($this->data, true), 'nabaztag');
		$this->data[$key] = array('data' => $data, 'seen' => false);
		Logger::debug(__METHOD__ . ' after modification: ' . print_r($this->data, true), 'nazaztag');
		$this->save();
	}

	/**
	* Set Configuration-Value
	*
	* @access public
	* 
	* @param String
	* @param mixed (String|Array)
	*/
	public function setConfig($key, $value) {
		Logger::debug($this->uid . ' - ' . __METHOD__, 'nabaztag');

		$this->configuration[$key] = $value;
		$this->saveConfiguration();
	}

	/**
	* Getter for Serialnumber
	*
	* @access public
	*
	* @return String
	*/
	public function getSerial() {
		Logger::debug($this->uid . ' - ' . __METHOD__, 'nabaztag');

		return $this->serial;
	}

	/**
	* Returns data by key
	*
	* @access public
	*
	* @param String
	*
	* @return mixed (String|Array)
	*/
	public function getData($key) {
		Logger::debug($this->uid . ' - ' . __METHOD__, 'nabaztag');

		$this->load();
		return (isset($this->data[$key]) ? $this->data[$key] : null);
	}

	/**
	* Returns all data
	*
	* @access public
	*
	* @return Array
	*/
	public function getAllData() {

		$this->load();

		return $this->data;
	}

	/**
	* Returns config by key
	*
	* @access public
	*
	* @param String
	*
	* @return mixed (String|Array)
	*/
	public function getConfig($key) {
		Logger::debug($this->uid . ' - ' . __METHOD__, 'nabaztag');

		$this->load();
		return (isset($this->configuration[$key]) ? $this->configuration[$key] : null);
	}

	/**
	* Returns all config
	*
	* @access public
	*
	* @return Array
	*/
	public function getAllConfig() {

		$this->loadConfiguration();

		return $this->configuration;
	}

	/**
	* Remove key from config
	*
	* @access public
	*
	* @param String
	*/
	public function removeConfig($key) {
		Logger::debug($this->uid . ' - ' . __METHOD__, 'nabaztag');

		$this->load();
		if(isset($this->configuration[$key])) {

			unset($this->configuration[$key]);
			$this->saveConfiguration();
		}
	}

	/**
	* Returns array with all unseen data
	*
	* @access public
	*
	* @return Array
	*/
	public function getNewData() {
		Logger::debug($this->uid . ' - ' . __METHOD__, 'nabaztag');

		$this->load();
		$return = array();
		foreach($this->data as $key => $value) {

			if(isset($value) && (!isset($value['seen']) || $value['seen'] == false)) {

				if(isset($value['data'])) {

					$return[$key] = $value['data'];
				}
			}
		}

		return $return;
	}

	/**
	* Set data as seen by Nabaztag
	* Date will be appended too
	*
	* @access public
	* 
	* @param String
	* @param Bool
	*/
	public function setSeen($key, $value) {
		Logger::debug($this->uid . ' - ' . __METHOD__, 'nabaztag');

		$this->load();
		if(isset($this->data[$key])) {

			$this->data[$key]['seen'] = $value;
			$this->data[$key]['last'] = ($value ? time() : null);
		}
		$this->save();
	}

	/**
	* Saves data to cache-file
	*
	* @access public
	*/
	public function save() {
		Logger::debug($this->uid . ' - ' . __METHOD__, 'nabaztag');
		$content = serialize($this->data);

		if(!is_writeable($this->file)) {

			Logger::warn('File ' . $this->file . ' is not writeable', 'nabaztag');
		}

		$fh = fopen($this->file, 'w');
		fputs($fh, $content);
		fclose($fh);

		Logger::debug('File: ' . $this->file, 'nabaztag');
		Logger::debug("File after save " . file_get_contents($this->file), 'nabaztag');
	}

	/**
	* Saves configuration
	*
	* @access public
	*/
	public function saveConfiguration() {
		Logger::debug($this->uid . ' - ' . __METHOD__, 'nabaztag');

		$content = serialize($this->configuration);

		$fh = fopen($this->configFile, 'w');
		fputs($fh, $content);
		fclose($fh);

		Logger::debug("File " . $this->configFile, 'nabaztag');
		Logger::debug("File after save " . file_get_contents($this->configFile), 'nabaztag');
	}

	/**
	* Destructor
	*
	* Last try to save
	*
	* @access public
	*/
	public function __destruct() {
		Logger::debug($this->uid . ' - ' . __METHOD__, 'nabaztag');
	}
}

?>
