<?php
namespace tests\pages;

use \base;

/**
* @package tests.pages
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Chor extends base\Page {

	public $title = 'Choreographie';

	public function process() {

		$dirname = PATH_FILES . '/chor/choreographies/';

		$chor = $this->request->get('chor');

		$files = array();

		if(is_null($chor)) {

			$this->getFiles($dirname, $files);
		} else {

			$hex = array();

			$file = $dirname . $chor;
			$content = file_get_contents($file);

			for($i = 0; $i < strlen($content); $i++) {

				$char = substr($content, $i, $i + 1);

				$hex[] = sprintf("%02s", strtoupper(dechex(ord($char))));
			}

			$hexbak = $hex;

			$parsed = array();

			$slice = $this->slice($hex, 4);
			$this->pop($hex, 3);

			$blocks = array();
			while(($block = $this->readBlock($hex)) != array()) {

				$blocks[] = $block;
			}

			pr($blocks);
			pr($hexbak);
		}

		$this->set('files', $files);
	}

	private function readBlock(&$hex) {

		$block = array();

		$wait = array_shift($hex);
		$type = array_shift($hex);

		if(hexdec($type) > 0) {

			switch($type) {
				case '01':	// tempo
					$block['type'] = 'tempo';

					$data = hexdec(array_shift($hex));
					$block['data'] = $data;
					break;

				case '07':	// led
					$block['type'] = 'led';
					$block['wait'] = hexdec($wait);

					$data = $this->slice($hex, 6);

					$led = hexdec(array_shift($data));
					$block['led'] = $led;

					array_pop($data);
					array_pop($data);

					$block['color'] = join('', $data);
					break;

				case '08':	// ear
					$block['type'] = 'ear';
					$block['wait'] = hexdec($wait);

					$data = $this->slice($hex, 3);

					$ear = hexdec(array_shift($data));
					$block['ear'] = ($ear == 0 ? 'right' : 'left');

					$pos = hexdec(array_shift($data)); // 0 - 18
					$block['position'] = $pos;

					$direction = hexdec(array_shift($data));
					$block['direction'] = ($direction == 0 ? 'forward' : 'backward');
					break;

				default:

					pr("unknown: " . $type);
					break;
			}
		}

		return $block;
	}

	private function getFiles($path, &$result = array()) {

		$files = glob(rtrim($path, "/*") . "/*");

		foreach($files as $file) {

			if(is_dir($file)) {

				$this->getFiles($file, $result);
			} else {

				$result[] = $file;
			}
		}
	}

	private function slice(&$arr, $to) {

		$return = array_slice($arr, 0, $to);

		for($i = 0; $i < $to; $i++) {

			array_shift($arr);
		}

		return $return;
	}

	private function pop(&$arr, $to) {

		$return = array();

		for($i = 0; $i < $to; $i++) {

			$return[] = array_pop($arr);
		}

		$return = array_reverse($return);

		return $return;
	}

}

?>
