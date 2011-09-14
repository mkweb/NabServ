<?php
namespace nabserv\plugins;

use nabserv\Nabaztag;
use nabserv\MessageBlock;
use \base\Logger;

/**
* WeatherPlugin
*
* This Plugin forces Nabaztag to tell the Weather from Google API via TTS
*
* @package nabserv.plugins
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Weather extends Plugin {

	/**
	* Blocktype 0A for Message
	* @tutorial http://www.cs.uta.fi/hci/spi/jnabserver/documentation/index.html#blocks
	* @param String
	*/
	protected $blockType = '0A';

	/**
	* Called if Api has Requested this Plugin
	* Returns true if data is valid
	*
	* @access public
	*
	* @param String
	*
	* @return Boolean
	*/
	public function api($data) {

		if(strlen($data) < 1) {

			return false;
		}

		Nabaztag::getInstance(NAB_SERIAL)->setNewData('weather', $data);
		return true;
	}

	/**
	* Called by ping-request from Nabaztag
	* Returns true if there is something to do
	*
	* @access public
	* 
	* @return Boolean
	*/
	public function ping() {

		$data = Nabaztag::getInstance(NAB_SERIAL)->getNewData();

		if(isset($data['weather'])) {

			$url = "http://www.google.com/ig/api?weather=" . $data['weather'] . "&hl=de";
			$xml = utf8_encode(file_get_contents($url));

			preg_match_all('/<forecast_information>(.*)<\/forecast_information>/Uis', $xml, $tmp);
			$tmp = $tmp[1][0];
			$data = array();

			preg_match_all('/<(.*) data="(.*)"\/>/Uis', $tmp, $t);
			foreach($t[0] as $key => $value) {

				$data[trim($t[1][$key])] = trim($t[2][$key]);
			}
			
			preg_match_all('/<forecast_conditions>(.*)<\/forecast_conditions>/Uis', $xml, $tmp);
			$tmp = $tmp[1][0];

			preg_match_all('/<(.*) data="(.*)"\/>/Uis', $tmp, $t);
			foreach($t[0] as $key => $value) {

				$data[trim($t[1][$key])] = trim($t[2][$key]);
			}

			list($y, $m, $d) = explode("-", $data['forecast_date']);
			$date = sprintf("% der %s %d", $d, $this->getMonth($m), $y);

			$tmp = explode(", ", $data['city']);
			if(count($tmp) > 0) {

				$tmp = array($tmp[0]);
			}
			$data['city'] = $tmp[0];

			$string = "Morgen, " . $date . " in " . $data['city'] . ", " . $data['low'] . " bis " . $data['high'] . " Grad Celsius";

			$mb = MessageBlock::getInstance(rand(11111, 99999));
			$mb->addLocalStream('broadcast/broad/tts/' . urlencode($string));

			$this->data = $mb->getHex();

			Nabaztag::getInstance(NAB_SERIAL)->setSeen('weather', true);
			return true;
		}

		return false;
	}

	/**
	* Replace integer month with it's german name for tts
	*
	* @access private
	*
	* @return String
	*/
	private function getMonth($m) {

		$mapping = array(
			1 => 'Januar',
			2 => 'Februar',
			3 => 'März',
			4 => 'April',
			5 => 'Mai',
			6 => 'Juni',
			7 => 'Juli',
			8 => 'August',
			9 => 'September',
			10 => 'Oktober',
			11 => 'November',
			12 => 'Dezember'
		);

		$m = intval($m);

		return $mapping[$m];
	}
}

?>
