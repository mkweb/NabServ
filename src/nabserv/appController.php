<?php
namespace nabserv;

use \base;

/**
* @package nabserv
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class AppController {

	private $nabaztag;
	private $available = array();
	private $inuse = array();

	private $apps = array();

	protected $userdata = array();

	private static $instances = array();

	public static function getInstance($serial) {

		if(!isset(self::$instances[$serial])) {

			self::$instances[$serial] = new AppController($serial);
		}

		return self::$instances[$serial];
	}

	public function __construct($serial) {

		$this->nabaztag = Nabaztag::getInstance($serial);
	}

	public function loadApps() {
	
		$apps = $this->nabaztag->getConfig('apps');

		if(is_array($apps)) {
			foreach($apps as $app => $data) {

				$value = null;
				if(strstr($app, '-') !== false) {

					$value = $app;
					list($app, $cnt) = explode('-', $app);
				}

				$classname = 'nabserv\\apps\\' . ucfirst($app);

				if(class_exists($classname)) {

					$obj = new $classname($this->nabaztag);
					$obj->setUserData($data);

					if(!is_null($value)) {

						$obj->prepare($value);
					}

					$this->apps[] = $obj;
				}
			}
		}
	}

	public function executeCrons() {

		foreach($this->apps as $app) {

			if($app->executeCrontab()) {

				break;
			}
		}
	}

	public function executePings() {

		foreach($this->apps as $app) {

			if($app->executePing()) {

				break;
			}
		}
	}

	public function executeActions($action) {

		foreach($this->apps as $app) {

			$app->executeAction($action);
		}
	}

	public function setUserData($data) {

		$this->userdata = $data;
	}

	public function handle($request = null) {

		$this->request = $request;

		if(!is_array($this->request)) {

			$this->request = explode(',', $this->request);
		}

		$method = (isset($this->request[0]) ? $this->request[0] : null);

		if(is_null($method)) {

			echo 'No method specified';
			exit;
		}

		if(!method_exists($this, $method)) {

			echo 'Method ' . htmlspecialchars($method) . ' does not exist';
		}

		$this->loadAvailable();

		$this->$method();
	}

	private function loadAvailable() {

		$path = PATH_SRC . DS . TARGET_NAMESPACE . DS . 'apps';

		$files = glob($path . DS . '*');
		foreach($files as $file) {

			$info = pathinfo($file);
			$filename = $info['filename'];

			if($filename != 'app') {

				$classname = 'nabserv\\apps\\' . ucfirst($filename);

				$app = new $classname($this->nabaztag);
				$app->prepare();

				if($app->getData('inuse') == true) {

					$this->inuse[$filename] = $app;
				} else {

					$this->available[$filename] = $app;
				}

				if($app->getData('multiple') == true) {

					$this->available[$filename] = $app;
				}
			}
		}
	}

	public function getall() {

		$result = array('available' => array(), 'inuse' => array());
		foreach($this->available as $app) {

			$result['available'][] = $app->getData();
		}
		
		foreach($this->inuse as $app) {

			$result['inuse'][] = $app->getData();
		}

		if(!is_null($this->nabaztag->getConfig('apps'))) {

			$allapps = array_keys($this->nabaztag->getConfig('apps'));

			foreach($allapps as $key => $value) {

				if(!isset($this->inuse[$value])) {

					if(strstr($value, '-') !== false) {

						list($code, $cnt) = explode('-', $value);

						$classname = 'nabserv\\apps\\' . ucfirst($code);
						if(class_exists($classname)) {

							$obj = new $classname($this->nabaztag);
							$obj->setUserData($this->inuse[$value]);
							$obj->prepare($value);

							$result['inuse'][] = $obj->getData();
						}
					}
				}
			}
		}

		echo json_encode($result);
	}

	public function useapp() {

		$code = $this->request[1];

		$current = $this->nabaztag->getConfig('apps');

		if(is_null($current)) {

			$current = array();
		}

		$newcode = $code;
		$i = 0;
		while(isset($current[$newcode]) && count($current[$newcode]) > 0) {

			$i++;
			$newcode = $code . '-' . $i;
		}

		if($i > 0) {

			$code  = $newcode;
		}

		$current[$code] = array();

		$this->nabaztag->setConfig('apps', $current);

		$result = array('result' => true);

		echo json_encode($result);
	}

	public function removeapp() {

		$code = $this->request[1];

		$current = $this->nabaztag->getConfig('apps');

		if(!is_null($current) && isset($current[$code])) {

			unset($current[$code]);
		}

		$this->nabaztag->setConfig('apps', $current);

		$result = array('result' => true);

		echo json_encode($result);
	}

	public function config() {

		$request = $this->request;

		array_shift($request); 		// remove "config"
		$code = array_shift($request);

		$data = array();
		foreach($request as $val) {

			list($key, $value) = explode('=', $val);
			$data[$key] = $value;
		}

		$current = $this->nabaztag->getConfig('apps');
	
		// crontabs
		if(isset($data['event']) && $data['event'] == 'crontab' && isset($data['crontab'])) {

			unset($data['event']);
			$data = array($code => array('trigger' => $data));
		
			$data = $this->decodeJsonRecursive($data);

			if(!isset($current[$code]['trigger'])) $current[$code]['trigger'] = array();
			$current[$code]['trigger']['crontab'] = $data[$code]['trigger']['crontab'];

			$this->nabaztag->setConfig('apps', $current);
			return true;
		}

		// actions
		if(isset($data['event']) && $data['event'] == 'action' && isset($data['action'])) {

			unset($data['event']);
			$data = array($code => array('trigger' => $data));
		
			$data = $this->decodeJsonRecursive($data);

			if(!isset($current[$code]['trigger'])) $current[$code]['trigger'] = array();
			$current[$code]['trigger']['action'] = $data[$code]['trigger']['action'];

			$this->nabaztag->setConfig('apps', $current);
			return true;
		}

		$data = array($code => $data);

		$current = $this->array_merge_recursive_distinct($current, $data);

		$this->nabaztag->setConfig('apps', $current);
	}

	private function array_merge_recursive_distinct()
	{
		$aArrays = func_get_args();
		$aMerged = $aArrays[0];
	   
		for($i = 1; $i < count($aArrays); $i++)	{

			if (is_array($aArrays[$i])) {

				foreach ($aArrays[$i] as $key => $val) {

					if (is_array($aArrays[$i][$key])) {

						$aMerged[$key] = is_array($aMerged[$key]) ? $this->array_merge_recursive_distinct($aMerged[$key], $aArrays[$i][$key]) : $aArrays[$i][$key];
					} else {

						$aMerged[$key] = $val;
					}
				}
			}
		}
	   
		return $aMerged;
	}

	private function decodeJsonRecursive(Array $arr) {

		foreach($arr as $key => $value) {

			if(is_array($value)) {

				$arr[$key] = $this->decodeJsonRecursive($value);
			} else {

				$arr[$key] = (in_array(substr($value, 0, 1), array('[', '{')) ? json_decode(str_replace('__', ',', $value)) : $value);
			}
		}

		return $arr;
	}

	public function getcrontab() {

		array_shift($this->request); // remove "getcrontab"
		$request = join(',', $this->request);

		$request = json_decode($request);

		$result = array();
		if(!is_null($request)) {

			foreach($request as $app => $data) {

				foreach($data as $key => $d) {

					$crontab = new Crontab();
					$crontab->parseHomepageRequest($d);
				
					$result[$app][$key] = $crontab->getCrontab();
				}
			}
		}

		echo json_encode($result);
	}

	function gethomepageobj() {

		array_shift($this->request); // remove "getcrontab"
		$request = join(',', $this->request);

		$request = json_decode($request);

		if(!is_null($request)) {

			foreach($request as $app => $data) {

				if(count($data) > 0) {

					foreach($data as $key => $d) {

						$crontab = new Crontab();

						$result[$app][$key] = $crontab->getHomepageObj($app, $d);
					}
				} else {

					$result[$app] = array();
				}
			}
		} 

		echo json_encode($result);
	}
}

?>
