<?php
namespace homepage\pages;

use \Exception;
use \base\Config;
use \base\Page;
use \base\LookupTable;

use homepage\Request;
use homepage\UserManager;

/**
* Handler for homepage/api.php
*
* @package homepage.pages
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Api extends Page {

	/**
	* PageLogic
	*
	* @access public
	*/
	public function process() {

        try {

            $token = $this->request->get('token');
            $method = $this->request->get('m');

            $this->validateToken($token);

            if(is_null($method)) {

                $method = 'heartbeat';
            }

            if(!method_exists($this, $method)) {

                throw new Exception('Method ' . $method . ' does not exist.');
            }

            $result = $this->{$method}();

            echo serialize($result);

        } catch (Exception $e) {

            echo $e->getMessage();
        }
        exit;
	}

    /**
    * Heartbeat-Method to check Connection from Masterserver
    * Allways returns true
    *
    * @access private
    * @return boolean allways true
    */
    private function heartbeat() {

        return true;
    }

    /**
    * Search for user on this Server
    *
    * @access private
    * @return array
    */
    private function findUser() {

        $search = $this->request->get('s');

		$table = LookupTable::getInstance('user');
        $result = $table->search(array('username' => $search));

        $tmp = $result;
        $result = array();
        foreach($tmp as $user) {

            $result[] = $user['username'];
        }

        return $result;
    }

    /**
    * Checking Request-token with configured
    * 
    * @access private
    * @param string $token
    * @return boolean
    * @throws Exception
    */
    private function validateToken($token) {

        $correct = Config::read('security.token');

        if($token != $correct) {

            throw new Exception('Invalid token');
        }
    }
}

?>
