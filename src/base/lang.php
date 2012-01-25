<?php
namespace base;

/**
* @package base
*/
class Lang {

	private static $data = array();

	public static function init($lang = 'en') {

		$file = PATH_RES . DS . 'lang' . DS . $lang . '.properties';
		
		if(file_exists($file)) {

			$tmp = file($file);

			foreach($tmp as $key => $value) {

				$value = trim($value);

				if(substr($value, 0, 1) == ';') continue;

				if(strlen($value) > 0) {

					$key = substr($value, 0, strpos($value, '='));
					$value = substr($value, strpos($value, '=') + 2);

					$key = trim($key);
					$value = trim($value);
	
					$value = str_replace('%BASE_URL', BASE_URL, $value);

					self::$data[$key] = $value;
				}
			}
		}
	}

	public static function replace($text) {

		preg_match_all('/\[LANG:(.*)\]/Uis', $text, $tmp);

		$keys = array_unique($tmp[0]);

		foreach($keys as $placeholder) {

			$key = substr($placeholder, 6, -1);

			$replace = (array_key_exists($key, self::$data) ? self::$data[$key] : '');
			$text = str_replace($placeholder, $replace, $text);
		}

		return $text;
	}

	public static function getAll() {

		return self::$data;
	}

	public static function get($key, $replace = array()) {

		if(array_key_exists($key, self::$data)) {

			$translation = self::$data[$key];

			foreach($replace as $key => $value) {

				$translation = str_replace("%" . $key, $value, $translation);
			}

			return $translation;
		} else {

			return null;
		}
	}
}

?>
