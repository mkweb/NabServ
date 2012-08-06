<?php
namespace base;

/**
* @package base
*/
class LookupTable {

	private $tablename;
	private $filename;
	private $data;

	private static $instances = array();

	public static function getInstance($tablename) {

		if(!isset(self::$instances[$tablename])) {

			self::$instances[$tablename] = new LookupTable($tablename);
		}

		return self::$instances[$tablename];
	}

	public function __construct($tablename) {

		$this->tablename = $tablename;
		$this->filename  = PATH_DB . DS . 'lookup' . DS . $tablename . '.serialized';

		if(!file_exists($this->filename)) {

			touch($this->filename);
			chmod($this->filename, 0777);
		}
	
		$this->load();
	}

	public function add($data) {

		$this->data[] = $data;
		$this->save();
	}

	public function find($data, $returnkey = false, $ignoreCase = false) {

		$found = null;

		if(is_array($data)) {

			foreach($this->data as $savedkey => $saved) {

				$key = 0;
				$hit = false;
				foreach($data as $key => $value) {

					if(array_key_exists($key, $saved) && strtolower($saved[$key]) == strtolower($value)) {

						$hit = true;
						break;
					}
				}

				if($hit) {

					$found = ($returnkey ? $savedkey : $this->data[$savedkey]);
					break;
				}
			}
		} else {

			foreach($this->data as $key => $saved) {

				if(!is_array($saved) && $saved == $data) {

					$found = $data;
					$found = ($returnkey ? $key : $this->data[$key]);
				}
			}
		}

		return $found;
	}

	public function search($data = null) {

        if(is_null($data)) {

            return $this->data;
        }

		$found = array();

		if(is_array($data)) {

			foreach($this->data as $savedkey => $saved) {

				$key = 0;
				$hit = false;
				foreach($data as $key => $value) {

					if($value == '' || (array_key_exists($key, $saved) && stristr($saved[$key], $value) !== false)) {

					    $found[] = array($key => $saved[$key]);
					}
				}
			}
		} else {

			foreach($this->data as $key => $saved) {

				if(!is_array($saved) && stristr($saved, $data) !== false) {

					$found = $data;
					$found[] = $this->data[$key];
				}
			}
		}

		return $found;
	}

	public function remove($data) {

		$key = $this->find($data, true);

		if($key !== null) {

			unset($this->data[$key]);
			$this->save();

			return true;
		}

		return false;
	}

	public function modify($find, $replace) {

		$entry = $this->find($find, true);

		if($entry !== null) {

			$current = $this->data[$entry];
			foreach($replace as $key => $value) {

				$current[$key] = $value;
			}
			$this->data[$entry] = $current;
		}

		$this->save();
	}

	private function load() {

		$content = file_get_contents($this->filename);

		if(strlen($content) > 0) {

			$this->data = unserialize($content);
		} else {

			$this->data = array();
		}
	}

	private function save() {

		$content = serialize($this->data);

		$fh = fopen($this->filename, 'w');
		fputs($fh, $content);
		fclose($fh);
	}
}

?>
