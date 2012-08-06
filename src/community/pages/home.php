<?php
namespace community\pages;

use \base;
use homepage\Request;
use base\LookupTable;
use base\Lang;
use nabserv\Nabaztag;

/**
* Handler for community/home.php
*
* @package community.pages
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Home extends base\Page {

	/**
	* Page-Title
	*
	* @access public
	* @var String
	*/
	public $title = 'Demo API';

	/**
	* PageLogic
	*
	* @todo describe better for doc
	*
	* @access public
	*/
	public function process() {

        $request = new Request();
        $action = $request->get('action');

        $response = array(
            'result' => null,
            'error' => array(
                'message' => null,
                'code' => null
            )
        );

        if(!is_null($request->get('search'))) {

            $search = $request->get('search');

            $lookup = LookupTable::getInstance('nabaztag');

            if(NULL == ($nabaztag = $lookup->find(array('name' => $search)))) {

                $response['error']['message'] = Lang::get('community.error.rabitnotfound');
                $response['error']['code'] = 'ERR_RABIT_NOTFOUND';
            } else {

                $serial = $nabaztag['serial'];
                $nab = Nabaztag::getInstance($serial);

                if($action == 'sendmessage') {

                    $data = $request->get('data');
                    $url = BASE_URL . "/vl/api.php?sn=" . $nabaztag['serial'] . "&token=" . $nab->getConfig('token') . "&message=" . urlencode($data);

                    $result = file_get_contents($url);

                    $xml = simplexml_load_string($result);

                    $result = $xml->xpath('/NabServ/Api/result');
                    $result = strip_tags($result[0]->asXML());

                    if($result != 'true') {

                        $response['error']['message'] = Lang::get('community.error.messagefailed');
                        $response['error']['code'] = 'ERR_MESSAGE.FAILED';
                    } else {

                        $response['result'] = 'TRUE';
                    }
                }
            }
        }

        $this->set('response', $response);
    }
}

?>
