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

        $validate = $this->validate;

        if($validate !== true) {

            return $validate;
        }

		Nabaztag::getInstance(NAB_SERIAL)->setNewData('weather', $data);

		return true;
	}

    public function extractCode($name) {

        $code = null;

        if(strstr($name, '(') !== false) {

            preg_match_all('/\((.*)\)/Uis', $name, $tmp);

            $last = array_pop($tmp);
            $code = $last[1];
        }

        return $code;
    }

    public function validate(Array $data) {

        if(!isset($data['city']) || $data['city'] == '') {

            return array(
                'type' => 'error',
                'reason' => 'Es wurde keine Stadt angegeben.'
            );
        }

        $data['code'] = $this->extractCode($data['city']);

        try {

            $q = (!is_null($data['code']) ? $data['code'] : $data['city']);

            $w = new WeatherCom();
            if(!$w->searchByZipCode($q) && !$w->searchByName($q)) {

                $searchResults = $w->getSearchResult();

                return array(
                    'type' => 'choose', 
                    'key' => 'city',
                    'headline' => 'Der angegebene Ort konnte nicht eindeutig zugewiesen werden.',
                    'data' => $searchResults
                );
            }

        } catch (WeatherException $we) {

            return "Fehlermeldung des Wetterdienstes: " . $we->getMessage();
        }

        return true;
    }

    public function prepareData(Array $data) {

        if(isset($data['city'])) {

            $data['code'] = $this->extractCode($data['city']);
        }

        return $data;
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

		$config = Nabaztag::getInstance(NAB_SERIAL)->getAllConfig();

		if(isset($config['apps']['weather'])) {

            $city = $config['apps']['weather']['city'];
            $code = $config['apps']['weather']['code'];

            $q = (!is_null($code) ? $code : $city);

            try {

                $w = new WeatherCom();
                $w->search($q);
                $data['city'] = $w->getSearchResult()->item->name;

                $forecast = $w->getForecast();

                $date = new \DateTime();
                // $date->add(new DateInterval('P1D'));
                $dateString = $date->format('Y-m-d');

                if(isset($forecast[$dateString])) {

                    $forecast = $forecast[$dateString];

                    if(!isset($forecast['06:00'])) {

                        throw new WeatherException("Data missing", "No date for 06:00 found.");
                    }
                    
                    if(!isset($forecast['11:00'])) {

                        throw new WeatherException("Data missing", "No date for 11:00 found.");
                    }

                    $data['condition'] = $forecast['11:00']['txt'];

                    list($y, $m, $d) = explode("-", $dateString);
                    $dateTts = sprintf("% der %s %d", $d, $this->getMonth($m), $y);

                    $string = "Heute, " . $dateTts . " in " . $data['city'] . ", " . $forecast['06:00']['lowest'] . ' bis ' . $forecast['06:00']['hightest'] . ' und mittags ' . $forecast['11:00']['lowest'] . ' bis ' . $forecast['11:00']['hightest'] . " Grad Celsius, " . $data['condition'];
                }

            } catch (WeatherException $we) {

                $string = "Keine Wetterinformationen verfügbar.";
            }

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

/**
* Client for Weather.com API
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class WeatherCom
{
	const SEARCHTYPE_MIXED 		= 'index';
	const SEARCHTYPE_ZIPCODE	= 'plz';
	const SEARCHTYPE_NAME		= 'name';

	private $baseUrl 		    = "http://api.wetter.com";
	private $projectName 		= "opennabserv";
	private $apiKey			    = "20c2d7427154286fd00332a7df8f827c";

	private $searchString;
	private $cityCode;
	private $sarchResult;

    /**
    * Search CityCode by ZipCode
    *
    * @see search()
    *
    * @access public
    *
    * @param $zipCode
    * @result boolean
    */
	public function searchByZipCode($zipCode) {

		return $this->search($zipCode. self::SEARCHTYPE_ZIPCODE);
	}

    /**
    * Search CityCode by CityName
    *
    * @see search()
    *
    * @access public
    *
    * @param $zipCode
    * @result boolean
    */
	public function searchByName($name) {

		return $this->search($name, self::SEARCHTYPE_NAME);
	}

    /**
    * Search CityCode
    * 
    * If CityCode was found unique return is true.
    * If there are more than one matches return is false - The matches can be requested by getSearchResult().
    *
    * @see getSearchResult()
    *
    * @access public
    *
    * @throws WeatherException
    *
    * @param string     SearchQuery
    * @param string     SearchType [Default: SEARCHTYPE_MIXED]
    * @result boolean
    */
	public function search($q, $type = self::SEARCHTYPE_MIXED) {

		$checkSum = md5($this->projectName . $this->apiKey . $q);

		return $this->call('/location/' . $type . '/search/' . $q, $checkSum, true);
	}

    /**
    * This method returns SearchResult if search for CityCode had no unique result
    *
    * @access public
    * @return Array
    */
	public function getSearchResult() {

		return $this->searchResult;
	}

    /**
    * Setting CityCode for Forecast-Request
    *
    * @access public
    * @param string
    */
	public function setCityCode($cityCode) {

		$this->cityCode = $cityCode;
	}

    /**
    * Get CityCode
    *
    * @access public
    * @return string
    */
	public function getCityCode() {

		return $this->cityCode;
	}

    /**
    * Requesting ForeCast for selected CityCode
    *
    * @access public
    *
    * @throws WeatherException
    *
    * @param string [optional]
    * @return array
    */
	public function getForecast($cityCode = null) {

		if(!is_null($cityCode)) {

			$this->setCityCode($cityCode);
		}

		if(is_null($this->cityCode)) {

			throw new WeatherException("Application", "No CityCode found.");
		}

		$checkSum = md5($this->projectName . $this->apiKey . $this->cityCode);

		return $this->call('/forecast/weather/city/' . $this->cityCode, $checkSum);
	}

    /**
    * Calling Weather.com API
    *
    * @access private
    *
    * @throws WeatherException
    *
    * @param string     Path in API
    * @param checkSum   Generated MD5-Hash for Authentication
    * @param boolean    Switch if the Call is a search for CityCode [Default: false]
    * @return boolean|array
    */
	private function call($url, $checkSum, $search = false) {

		$url = ltrim($url, '/');
		$url = sprintf("%s/%s/project/%s/cs/%s", $this->baseUrl, $url, $this->projectName, $checkSum);

		$xml = file_get_contents($url);

		return $this->parseXml($xml, $search);
	}

    /**
    * Generating Result from ResponseXML
    *
    * @access private
    * 
    * @throws WeatherException
    *
    * @param string     XML-String from API
    * @param boolean    Switch if the Call is a search for CityCode [Default: false]
    * @return booean|array
    */
	private function parseXml($xmlStr, $search = false) {

		$xml = simplexml_load_string($xmlStr);

		$rootName = $xml->getName();

		if($rootName == 'error') {

			$this->error = true;

			throw new WeatherException($xml->title, $xml->message);
		} elseif($search == true) {

			if($xml->exact_match == 'no') {

				$result = array();
				foreach($xml->result->item as $item) {

					$string = $item->adm_1_code . '-' . $item->plz . ' ' . $item->name;

					$additionals = array();
					if(!empty($item->quarter)) {

						$quarter = (Array)$item->quarter;
						$additionals[] = $quarter[0];
					}
					
					if(!empty($item->adm_2_name)) {

						$name = (Array)$item->adm_2_name;
						$additionals[] = $name[0];
					}

					if(count($additionals) > 0) {

						$string .= ' (' . join(', ', $additionals) . ')';
					}

					$cityCode = (Array)$item->city_code;
					$cityCode = $cityCode[0];

					$result[$cityCode] = $string;
				}

				$this->searchResult = $result;

				return false;
			} else {

				$cityCode = (Array)$xml->result->item->city_code;

                $this->searchResult = $xml->result;
				$this->cityCode = $cityCode[0];

				return true;
			}
		} else {

            $forecast = array();

			$dates = $xml->forecast->date;

			foreach($dates as $dateItem) {
				
				$times = $dateItem->time;

				foreach($times as $timeItem) {

                    $fulltime = $this->getValue($timeItem->dhl);
                    list($date, $time) = explode(' ', $fulltime);

                    $lowest = $this->getValue($timeItem->tn);
                    $hightest = $this->getValue($timeItem->tx);
                    $txt = $this->getValue($timeItem->w_txt);

                    $forecast[$date][$time] = array('lowest' => $lowest, 'hightest' => $hightest, 'txt' => $txt);
				}
			}

            return $forecast;
		}
	}

    /**
    * Reading Value from SimpleXML-Attribute
    *
    * @access private
    * 
    * @param object
    * @return string
    */
    private function getValue($obj) {

        $tmp = (Array)$obj;
        return $tmp[0];
    }
}

/**
* WeatherException for Weather.com API
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class WeatherException extends \Exception {

	public function __construct($title, $message) {

		$this->message = sprintf("[%s] %s", $title, $message);
	}
}

?>
