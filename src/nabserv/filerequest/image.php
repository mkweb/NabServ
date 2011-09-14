<?php
namespace nabserv\filerequest;

use \nabserv;

use nabserv\Request;
use nabserv\Nabaztag;
use nabserv\AppController;

use \Exception;

/**
* @package nabserv.filerequest
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Image {

	private $data = array();
	private $ignorecache = false;

	public function execute() {

		$request = new Request();

		if($request->getData('ignorecache')) {

			$this->ignorecache = true;
		}

		$this->data = explode(',', $request->getData('d'));

		$this->getimage();
	}

	public function getimage() {

		$name = $this->data[0];
		$color = $this->data[1];
		$size = (isset($this->data[2]) ? $this->data[2] : null);
		$text = (isset($this->data[3]) ? $this->data[3] : null);

		$hash = md5(serialize($this->data));
		$cache = PATH_RES . DS . 'apps' . DS . 'images' . DS . 'cache' . DS . $hash . '.png';

		if(!$this->ignorecache) {
			if(file_exists($cache)) {

				header('Content-Type: image/png');
				readfile($cache);
				exit;
			}
		}

		if(null == $size) {

			$size = array('w' => 120, 'h' => 240);
		} else {

			$size = array('w' => $size, 'h' => ($size * 2));
		}

		$imagefile = PATH_RES . DS . 'apps' . DS . 'images' . DS . $name . '.png';
		$bgfile = PATH_RES . DS . 'apps' . DS . 'images' . DS . 'bg_' . $color . '.png';

		$bg = imagecreatefrompng($bgfile);

		$white = imageColorAllocate ($bg, 255, 255, 255);
		$trans = imagecolortransparent($bg,$white);

		$image = imagecreatefrompng($imagefile);

		$newimage = imagecreatetruecolor($size['w'], $size['h']);

		imagecopyresampled($newimage, $bg, 0, 0, 0, 0, $size['w'], $size['h'], 120, 240);
		imagecopyresampled($newimage, $image, 0, 0, 0, 0, $size['w'], $size['h'], 120, 240);

		if(null != $text) {

			$fontfile = PATH_RES . DS . 'fonts' . DS . 'Ubuntu-B.ttf';;
			$fontsize = 14;

			if(file_exists($fontfile)) {

				$fontwidth = $size['w'];
				while($fontwidth >= ($size['w'] - 10)) {

					$fontsize --;

					$box = imageftbbox($fontsize, 0, $fontfile, $text);
					$fontwidth = $box[2] - $box[0];
				}

				$white = imagecolorallocate($newimage, 0, 0, 0);
				imagettftext($newimage, $fontsize, 0, ($size['w'] / 2 - $fontwidth / 2), ($size['h'] - 10), $white, $fontfile, $text);
				imagettftext($newimage, $fontsize, 0, ($size['w'] / 2 - $fontwidth / 2), ($size['h'] - 10) - $size['w'], $white, $fontfile, $text);
			}
		}

		imagepng($newimage, $cache);

		header('Content-Type: image/png');
		imagepng($newimage);
	}

}

?>
