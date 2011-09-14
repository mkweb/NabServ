<?php
namespace demo\pages;

use \base;

/**
* Handler for demo/home.php
*
* @package demo.pages
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

		$pl = $this->request->get('pl');

		if($this->request->get('sn')) {

			$_SESSION['sn'] = $this->request->get('sn');
		}

		if($this->request->get('token')) {

			$_SESSION['token'] = $this->request->get('token');
		}
		
		$serial = (isset($_SESSION['sn']) ? $_SESSION['sn'] : '');
		$token = (isset($_SESSION['token']) ? $_SESSION['token'] : '');

		$inputs = array();

		if($pl != '') {

			$plugin = "nabserv\\plugins\\" . ucfirst($pl);
			
			$plugin = new $plugin;

			$demo = $plugin->demo();

			$inputs = array();

			if(isset($demo['type'])) {

				$inputs[] = '<tr valign="top">' . $this->createInputs($demo) . '</tr>';
			} elseif(is_array($demo)) {

				foreach($demo as $d) {

					$inputs[] = '<tr valign="top">' . $this->createInputs($d) . '</tr>';
				}
			}
		}

		$result = null;

		if($this->request->get('send')) {

			ob_start();

			$process = true;

			if($serial == '') {

				echo "No serial provided :(<hr />";
				$process = false;
			}

			$d = $this->request->get('d');

			if(is_array($d)) {
				if(method_exists($plugin, 'handleDemo')) {

					$d = $plugin->handleDemo($d);
				} else {

					echo "Plugin has to provide a \"handleDemo\"-Method!<hr />";
					$process = false;
				}
			}

			if($process) {
				$url = 		BASE_URL . '/vl/api.php?sn=' . $serial . '&token=' . $token . '&' . $pl . (isset($d) ? '=' . urlencode($d) : '');
				$url_show = 	BASE_URL . '/vl/api.php?sn=' . $serial . '&token=' . $token . '&' . $pl . (isset($d) ? '=' . $d : '');

				$result = file_get_contents($url);
				
				echo "URL: <a href=\"" . $url . "\" target=\"_blank\">" . $url_show . "</a><br /><br />RESULT: " . highlight_string($result, true);
			}

			$result = ob_get_clean();
		}

		$this->set('result', $result);
		$this->set('inputs', $inputs);
		$this->set('serial', $serial);
		$this->set('token', $token);
	}

	/**
	* Dirty HTML-Creator for Demo InputFields
	*
	* @access private
	*
	* @param Array
	*/
	private function createInputs($demo) {

		$input = null;

		if($demo['type'] == 'dropdown') {

			if(isset($demo['name'])) $input .=  '<td>' . $demo['name'] . ":</td><td>";

			$name = "d";
			if(isset($demo['id'])) {

				$name = "d[" . $demo['id'] . "]";
			}
			$input .=  '<select name="' . $name . '">';
			foreach($demo['value'] as $key => $value) {
				$input .=  '<option value="' . $key . '"' . (isset($_GET['d']) && $_GET['d'] == $key ? ' selected="selected"' : '') . '>' . $value . '</option>';
			}
			$input .=  '</select></td>';
		}
		if($demo['type'] == 'text') {

			$input .=  '<td>' . $demo['name'] . ':</td><td><input type="text" name="d" value="' . (isset($_GET['d']) ? $_GET['d'] : $demo['value']) . '" /></td>';
		}
		if($demo['type'] == 'textarea') {

			$input .=  '<td>' . $demo['name'] . ':</td><td><textarea name="d" rows="6" cols="60">' . (isset($_GET['d']) ? $_GET['d'] : $demo['value']) . '</textarea></td>';
		}

		return $input;
	}
}

?>
