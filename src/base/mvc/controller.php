<?php
namespace base\mvc;

use homepage\Request;

/**
* BaseClass for MVC-Framework
*
* @package base.mvc
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Controller {

	/**
	* Default PageCode if none set
	*
	* @access private
	* @var String
	*/
	private $pagecode = 'home';

	/**
	* @access private
	* @var homepage\Request
	*/
	private $request;
	
	/**
	* Every available prepared Page
	* This is used for ActionDetection
	*
	* @access private
	* @var Array
	*/
	private $pages = array();

	/**
	* Constructor
	*
	* @access public
	* 
	* @param homepage\Request
	*/
	public function __construct(Request $request) {

		$this->request = $request;

		if($request->get('page')) {

			$this->pagecode = $request->get('page');
		}

		$this->loadAvailablePages();
		$this->processActions($request);

		$this->generate();
	}

	/**
	* Parsing projects page-directory and prepares available pages
	*
	* Default "page.php" is ignored
	*
	* @access private
	*/
	private function loadAvailablePages() {

		$files = glob(PATH_SRC . DS . TARGET_NAMESPACE . DS . 'pages' . DS . '*');

		foreach($files as $file) {

			$name = basename($file);

			if($name != 'page.php') {

				$tmp = explode('.', $name);
				array_pop($tmp);

				$code = join('.', $tmp);

				$name = TARGET_NAMESPACE . '\\pages\\' . ucfirst($code);
				$this->pages[$code] = new $name($this->request);
			}
		}
	}

	/**
	* Looping throught prepare Pages and executes "processAction" if this method exists in it
	*
	* if processAction returns true, Browser will reload or redirect to specified location
	*
	* @access private
	*/
	private function processActions() {

		$done = false;
		$redirect = null;

		foreach($this->pages as $code => $page) {

			if(method_exists($page, 'processAction')) {

				$res = $page->processAction();

				if($res) {

					$done = true;
				}

				if(!is_null($page->getRedirect())) {

					$redirect = $page->getRedirect();
				}
			}
		}

		if(!is_null($redirect)) {

			header('Location: ' . BASE_URL . '/?page=' . $redirect);
			exit;
		}

		if($done) {

			$this->reload();
		}
	}

	/**
	* Triggers Page-Methods in following direction and returns generated HTML
	*
	* <ul>
	*   <li>currentPage::process()</li>
	*   <li>currentPage::getContent()</li>
	*   <li>mainPage::process()</li>
	*   <li>mainPage::getContent()</li>
	* </ul>
	*
	* @access private
	*
	* @return String
	*/
	private function generate() {

		if(array_key_exists($this->pagecode, $this->pages)) {

			$page = $this->pages[$this->pagecode];

			$page->process();

			$title = $page->title;
			$content = $page->getContent();
		}

		$mainpage = $this->pages['main'];
		$mainpage->set('title', $title);
		$mainpage->set('content', $content);

		$mainpage->addData($page->getData());

		$mainpage->process();

		echo $mainpage->getContent();
	}

	/**
	* Sending Location-Header to Browser
	*
	* @access private
	*/
	private function reload() {

		header('Location: /?' . http_build_query($_GET));
		exit;
	}
}

?>
