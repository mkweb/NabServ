<?php
namespace homepage\helpers;

/**
* Class ValidateAble
*
* Childclasses provide a Method validate() which will validate UserInput with data describe in $this->validate
*
* @package homepage.helpers
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class ValidateAble {

	/**
	* ValidationRules
	*
	* @access protected
	* @var Array
	*/
	protected $validate 	= array();

	/**
	* UserData
	*
	* @access proteced
	* @var Array
	*/
	protected $data 	= array();

	/**
	* Store for ValidationErrors
	*
	* @access private
	* @var Array
	*/
	private   $_errors	= array();

	/**
	* Triggering _doValidation() for every UserData for which a rule exists
	*
	* Returns true if no errors occured
	*
	* @access public
	*
	* @return boolean
	*/
	public function validate() {

		foreach($this->data as $key => $value) {

			if(!is_array($value) && isset($this->validate[$key])) {

				if(isset($this->validate[$key]['message'])) {

					$validationRule = $this->validate[$key];
					$this->_doValidation($value, $validationRule);

				} else {
					foreach($this->validate[$key] as $validationRule) {

						$this->_doValidation($value, $validationRule);
					}	

				}
			}
		}

		return (count($this->_errors) < 1 ? true : false);
	}

	/**
	* Validates Value with given rule
	*
	* Available rules are
	* <ul>
	*   <li>length</li>
	*   <li>minlength</li>
	*   <li>maxlength</li>
	*   <li>regexp</li>
	*   <li>func</li>
	* </ul>
	*
	* @access private
	*/
	private function _doValidation($value, $rule) {

		if(isset($rule['length'])) {

			list($from, $to) = explode('-', $rule['length']);

			if(strlen($value) < $from || strlen($value) > $to) {

				$this->_errors[] = $rule['message'];
			}
		}

		if(isset($rule['minlength'])) {

			if(strlen($value) < $rule['minlength']) {

				$this->_errors[] = $rule['message'];
			}
		}

		if(isset($rule['maxlength'])) {

			if(strlen($value) > $rule['minlength']) {

				$this->_errors[] = $rule['message'];
			}
		}

		if(isset($rule['regexp'])) {

			if(!preg_match($rule['regexp'], $value)) {

				$this->_errors[] = $rule['message'];
			}
		}

		if(isset($rule['func'])) {

			$func = 'validate_' . ucfirst($rule['func']);

			if(method_exists($this, $func) && !$this->$func($value)) {

				$this->_errors[] = $rule['message'];
			}
		}
	}

	/**
	* Returns Array with errors
	*
	* @access public
	*
	* @return Array
	*/
	public function getValidationErrors() {

		return $this->_errors;
	}
}

?>
