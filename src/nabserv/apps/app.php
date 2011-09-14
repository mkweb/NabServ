<?php
namespace nabserv\apps;

use nabserv\Nabaztag;

/**
* package nabserv.apps
*/
abstract class App {

	protected $userdata;

	protected $data = array(
		'code' 		=> null,
		'name' 		=> null,
		'description' 	=> null,
		'inuse'		=> false,
		'config'	=> array(),
		'image' 	=> null,
		'multiple' 	=> false
	);
	
	protected $nabaztag;

	public function __construct(Nabaztag &$nabaztag) {

		$this->nabaztag = $nabaztag;
	}

	public function prepare($code = null) {

		$code = (is_null($code) ? $this->data['code'] : $code);

		$used = $this->nabaztag->getConfig('apps');

		if($used && is_array($used)) {

			if(array_key_exists($code, $used)) {

				$inuse = true;
				$this->data['inuse'] = true;
				$this->data['config'] = $used[$code];
			}
		}

		$this->data['valid'] = false;

		$color = 'blue';
		if($this->data['inuse']) {

			$color = 'red';
			if($this->dataValid($used[$code])) {

				$this->data['valid'] = true;
				$color = 'green';
			}
		}

		$this->data['image'] = BASE_URL . "/vl/image.php?&d=" . $this->data['code'] . "," . $color;
		$this->data['code'] = $code;
	}

	public function setUserData($data) {

		$this->userdata = $data;
	}

	abstract function onPing();
	abstract function onCron();
	abstract function onAction();
	abstract function execute();

	public function executeCrontab() {

		if(isset($this->userdata['trigger']['crontab'])) {

			$crontab = $this->userdata['trigger']['crontab'];

			foreach($crontab as $c) {

				if($this->cronValid($c)) {

					$this->onCron();

					return true;
				}
			}
		}

		return false;
	}

	public function executePing() {

		$this->onPing();
	}

	public function executeAction($action) {

		if(isset($this->userdata['trigger']['action'])) {

			if(is_array($this->userdata['trigger']['action']) && in_array($action, $this->userdata['trigger']['action'])) {

				$this->onAction();
			}
		}
	}
	
	protected function sendApi($plugin, $data) {

		$url = BASE_URL . "/vl/api.php?sn=" . NAB_SERIAL . "&token=" . NAB_TOKEN . "&" . $plugin . "=" . urlencode($data);
		file_get_contents($url);
	}

	protected function dataValid($used) {

		$allvalid = true;
		foreach($this->data['needed'] as $key => $value) {

			if(!array_key_exists($key, $used)) {

				$allvalid = false;
			} else {

				if(!$this->validate($key, $used[$key], $used)) {

					$allvalid = false;
				}
			}
		}

		return $allvalid;
	}

	public function validate($key, $value, $all) {
		
		if($key == 'trigger') {

			if(isset($value['crontab']) && count($value['crontab']) > 0) {

				return true;
			}
			
			if(isset($value['action']) && count($value['action']) > 0) {

				return true;
			}
		}

		return false;
	}

	public function getData($key = null) {

		if(is_null($key)) {

			return $this->data;
		}

		return (isset($this->data[$key]) ? $this->data[$key] : null);
	}

	public function cronValid($crontab) {

		$current_weekday = (date('N') == 7 ? 0 : date('N'));
		$current_hour    = date('H');
		$current_minute  = date('i');

		$isRandom = false;
		if(substr($crontab, 0, 7) == 'random:') {

			list($tmp, $wd) = explode(' ', $crontab);
			$tmp = explode(':', $tmp);
			$cnt = $tmp[1];

			$isRandom = true;
		} else {

			list($m, $h, $d, $m, $wd) = explode(' ', $crontab);
		}

		// checking weekday
		if($wd != '*') {

			$tmp = explode(',', $wd);
			if(!in_array($current_weekday, $tmp)) {

				return false;
			}
		}

		if(!$isRandom) {

			// checking hour
			if($h != '*') {

				if($h != intval($current_hour)) {

					return false;
				}
			}

			// checking minute
			if($m != '*') {

				$tmp = explode(',', $m);
				if(!in_array($current_minute, $tmp)) {

					return false;
				}
			}

			if($last = $this->getLastCronRun($crontab)) {

				if(time() - $last < 60) {
					
					return false;
				}
			}
		} else {

			$last = $this->getLastCronRun($crontab);

			$now = time();
			$factor = (86400 / $cnt);

			$start = mktime(0, 0, 0);

			while($start < ($now - $factor)) {

				$start += $factor;
				$end    = $start + $factor;
			}

			if($start < $now && $end > $now && rand(0, 100) == 1) {

				if(!is_null($last)) {

					if($last > $start) {

						return false;
					}
				}
			} else {

				return false;
			}
		}

		$this->setLastCronRun($crontab);

		return true;
	}

	private function setLastCronRun($c) {

		$current = $this->nabaztag->getConfig('lastcron');
		
		if(null == $current) {

			$current = array();
		}

		$current[$this->data['code']][$c] = time();

		$this->nabaztag->setConfig('lastcron', $current);
	}

	private function getLastCronRun($c) {

		$data = $this->nabaztag->getConfig('lastcron');

		if(is_array($data) && array_key_exists($this->data['code'], $data) && isset($data[$this->data['code']][$c])) {

			return $data[$this->data['code']][$c];
		}

		return null;
	}
}

?>
