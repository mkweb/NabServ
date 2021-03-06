<?php
namespace nabserv\apps;

use \DomDocument;

/**
* package nabserv.apps
*/
class Rss extends App {

	protected $data = array(
		'code' 		=> 'rss',
		'name' 		=> 'RSS',
		'description' 	=> 'Lass dir von deinem Nabaztag einen RSS-Feed vorlesen',
		'inuse'		=> false,
		'needed'	=> array(
			'url'  => array('type' => 'text', 'description' => 'Feed URL:')
		),
		'multiple'	=> true
	);

	public function validate($key, $value, $all) {

		if(parent::validate($key, $value, $all)) {

			return true;
		}

		if($key == 'url') {

			if(strlen($value) > 0) {

				return true;
			}
		}

		return false;
	}

	public function execute(){

		$timeout = 60;

		$data = $this->nabaztag->getConfig('apps');

        foreach($data as $key => &$dat) {
        
            if(substr($key, 0, 3) == 'rss') {

                $url = (isset($dat['url']) ? $dat['url'] : null);
                $lastts = (isset($dat['lastts']) ? $dat['lastts'] : null);
                $savedheadlines = (isset($dat['headlines']) ? $dat['headlines'] : array());

                // If never read or last read at least 10 minutes ago
                if(is_null($lastts) || (!is_null($url) && (is_null($lastts) || $lastts < time() - $timeout))) {

                    $dom = new DomDocument();
                    $dom->load($url);

                    $title = $dom->getElementsByTagName('title')->item(0)->nodeValue;

                    $headlines = array();
                    foreach($dom->getElementsByTagName('item') as $node) {

                        $headlines[] = $node->getElementsByTagName('title')->item(0)->nodeValue;
                    }

                    $new = array();
                    foreach($headlines as $head) {

                        if(!in_array($head, $savedheadlines)) {

                            $new[] = $head;
                        }
                    }

                    if(count($new) < 1) {

                        continue;
                    }

                    if(count($new) == 1) {

                        $msg = '';

                        if(strlen($new[0]) <= 100) {

                            $message = ' - ' . $new[0];
                        }

                        $this->sendApi('tts', 'Ein neuer Beitrag im RSS Feed ' . $title . $msg);
                    } else {

                        $this->sendApi('tts', count($new) . ' neue Beiträge im RSS Feed ' . $title);
                    }

                    $dat['lastts'] = time();
                    $dat['headlines'] = $headlines;
                }
            }
        }

        $this->nabaztag->setConfig('apps', $data);
	}

	public function onPing() {

		$this->execute();
	}

	public function onCron(){

		$this->execute();
	}

	public function onAction(){

		$this->execute();
	}
}

?>
