<?php
namespace nabserv;

class Crontab {

	private $random  = null;

	private $daymapping = array('son', 'mon', 'die', 'mit', 'don', 'fre', 'sam');

	private $minute  = 0;
	private $hour	 = 0;
	private $day     = 0;
	private $month   = 0;
	private $weekday = 0;

	public function parseHomepageRequest(\stdClass $obj) {

		$daymapping = array_flip($this->daymapping);

		// periodic
		if(is_numeric($obj->period)) {

			if($obj->period == 60) {

				$m = 0;
			}

			if($obj->period == 30) {

				$m = '0,30';
			}

			if($obj->period == 15) {

				$m = '0,15,30,45';
			}

			$this->minute  = $m;
			$this->hour    = '*';
			$this->day     = '*';
			$this->month   = '*';
		} else {

			if($obj->period == 'exact') {

				list($h, $m) = explode('-', $obj->exact);

				$this->minute = $m;
				$this->hour   = $h;
				$this->day     = '*';
				$this->month   = '*';
			} 

			if($obj->period == 'random') {

				$this->random = $obj->random;
			}
		}

		// parsing days
		$days = array();
		foreach($obj->days as $day => $active) {

			if($active) {

				$days[] = $day;
			}
		}

		if(count($days) == 7) {

			$this->weekday = '*';
		} else {

			$tmp = array();
			foreach($days as $day) {

				$tmp[] = $daymapping[$day];
			}
			$this->weekday = join(',', $tmp);
		}
	}

	public function getHomepageObj($id, $crontab) {

		$plain = array(
			'id' 	 => $id,
			'crontab' => $crontab,
			'period' => null,
			'random' => null,
			'exact'  => null,
			'days'   => array(
				'mon' => false,
				'die' => false,
				'mit' => false,
				'don' => false,
				'fre' => false,
				'sam' => false,
				'son' => false
			)
		);

		list($minute, $hour, $day, $month, $weekday) = explode(' ', $crontab);

		if(substr($crontab, 0, 7) == 'random:') {

			list($time, $weekday) = explode(' ', substr($crontab, 7));

			$plain['period'] = 'random';
			$plain['random'] = $time;
		} elseif($minute == '0,15,30,45' && $hour == '*') {

			$plain['period'] = 15;
		} elseif($minute == '0,30' && $hour == '*') {

			$plain['period'] = 30;
		} elseif($minute == '0' && $hour == '*') {
		
			$plain['period'] = 60;
		} else {
			$plain['period'] = 'exact';
			$plain['exact'] = $hour . '-' . $minute;
		}

		if($weekday == '*') {

			foreach($plain['days'] as $key => $value) {

				$plain['days'][$key] = true;
			}
		} else {

			$tmp = explode(',', $weekday);
			foreach($tmp as $num) {

				$plain['days'][$this->daymapping[$num]] = true;
			}
		}

		return $plain;
	}

	public function getCrontab() {

		if(!is_null($this->random)) {

			return 'random:' . $this->random . ' ' . $this->weekday;
		}

		return sprintf("%s %s %s %s %s", $this->minute, $this->hour, $this->day, $this->month, $this->weekday);
	}
}

?>
