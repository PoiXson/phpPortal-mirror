<?php
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2019
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal;

//use pxn\phpUtils\Strings;
//use pxn\phpUtils\System;
//use pxn\phpUtils\General;
//use pxn\phpUtils\Defines;


abstract class WebApp extends \pxn\phpUtils\app\App {

//	protected $pageName      = NULL;
//	protected $pageObj       = NULL;
//	protected $pageContents  = NULL;
//	protected $captureBuffer = NULL;

	protected $menu = [];
//	protected $args = [];

//	protected $globalTwigs = [];



	public function __construct() {
		parent::__construct();
//		// default render types
//		$this->registerRender( new \pxn\phpPortal\render\RenderWebMain   ($this) );
//		$this->registerRender( new \pxn\phpPortal\render\RenderWebSplash ($this) );
//		$this->registerRender( new \pxn\phpPortal\render\RenderWebMinimal($this) );
	}



/*
	protected function initApp() {
		parent::initApp();
		ConfigPortal::setPageRef($this->pageName);
		// get page name from get/post values
		if (isset($_GET['page']) || isset($_POST['page'])) {
			$page = General::getVar('page', 'str', ['get', 'post']);
			if (!empty($page)) {
				ConfigPortal::setPageName($page);
			}
		}
		// get page name from url path
		if (empty($this->pageName) && isset($_SERVER['REQUEST_URI'])) {
			$urlPath = $_SERVER['REQUEST_URI'];
			$urlPath = Strings::Trim($urlPath, ' ', '/');
			if (!empty($urlPath)) {
				$parts = \explode('/', $urlPath);
				if (\count($parts) > 0) {
					if (isset($parts[0]) && !empty($parts[0])) {
						ConfigPortal::setPageName($parts[0]);
						unset($parts[0]);
						$this->setArgs(
							\array_values($parts)
						);
					}
				}
			}
		}
	}
	public function terminating() {
		// dump captured buffer, but not page contents
		if ($this->captureBuffer !== NULL) {
			$buffer = &$this->captureBuffer;
			// stop buffer capture
			\ob_end_flush();
			// dump buffer
			if (!empty($buffer)) {
				echo $buffer;
			}
			unset($buffer);
			$this->captureBuffer = NULL;
		}
	}



	// app weight
	protected function getWeight() {
		return System::isShell()
			? -1
			: 1000;
	}



	public function setPage($page) {
		if ($this->pageObj != NULL) {
			$pageTitle = (
				$page instanceof \pxn\phpPortal\Page
				? $page->getTitle()
				: (string) $page
			);
			$pageNameCurrent = $this->pageName;
			fail("Unable to set page to: $pageTitle  Already set to: $pageNameCurrent",
				Defines::EXIT_CODE_USAGE_ERROR);
		}
		if ($page instanceof \pxn\phpPortal\Page) {
			$this->pageObj = $page;
			ConfigPortal::setPageName(
				$page->getName()
			);
		} else {
			ConfigPortal::setPageName(
				$page
			);
		}
	}



	// load page
	public function getPageObj() {
		// page object already loaded
		if ($this->pageObj != NULL) {
			return $this->pageObj;
		}
		// get page name
		$pageName = ConfigPortal::getPageName();
		if (empty($pageName)) {
			fail('Page name could not be detected!',
				Defines::EXIT_CODE_INVALID_FORMAT);
		}
		// search paths
		$classPaths = [
			// website page class
			"\\{$this->classpath}\\pages\\page_{$pageName}",
			// internal page class
			"\\pxn\\phpPortal\\pages\\page_{$pageName}",
		];
		// find page class
		foreach ($classPaths as $clss) {
			if (\class_exists($clss, TRUE)) {
				$this->pageObj = new $clss($this);
				break;
			}
		}
		// page class found
		if ($this->pageObj != NULL) {
			return $this->pageObj;
		}
		// forward to 404 page
		if ($pageName != '404') {
			@\http_response_code(404);
			$this->setArg(
				ConfigPortal::getFailedPage(),
				$pageName
			);
			$this->pageName = '404';
			$this->pageObj = NULL;
			return $this->getPageObj();
		}
		// 404 page not found
		$this->pageObj = NULL;
		$this->pageContents = "\n<h1>404 - Page Not Found!</h1>\n\n";
		return NULL;
	}
	public function getTitle() {
		$page = $this->getPageObj();
		if ($page == NULL) {
			return NULL;
		}
		return $page->getTitle();
	}
	// called from render class (has internal buffering)
	public function &getPageContents() {
		if (!empty($this->pageContents)) {
			return $this->pageContents;
		}
		$page = $this->getPageObj();
		if ($page != NULL && $page instanceof \pxn\phpPortal\Page) {
			// start buffer capture
			$buffer = &$this->captureBuffer;
			$buffer = '';
			\ob_start(
				function($buf) use (&$buffer) {
					$buffer .= $buf;
				}
			);
			// get page contents
			$result = $page->getPageContents();
			if ($this->pageContents == NULL) {
				$this->pageContents = '';
			}
			// stop buffer capture
			\ob_end_flush();
			if (!empty($buffer)) {
				$this->pageContents .= "{$buffer}\n\n\n";
			}
			unset($buffer);
			$this->captureBuffer = NULL;
			// append page contents
			if ($result !== NULL) {
				if ($result === FALSE) {
//TODO:
					$this->pageContents .= "Failed to render page!\n\n\n";
				} else
				if ($result !== TRUE) {
					if (\is_array($result)) {
						$result = Arrays::Flatten($result);
						foreach ($result as $line) {
							if (!empty($line)) {
								$this->pageContents .= "{$line}\n\n\n";
							}
						}
					} else {
						$result = (string) $result;
						$this->pageContents .= "{$result}\n\n\n";
					}
				}
			}
		}
		return $this->pageContents;
	}



	public function getArg($key) {
		if (isset($this->args[$key])) {
			return $this->args[$key];
		}
		return NULL;
	}
	public function getArgs() {
		return $this->args;
	}
	public function setArg($key, $value) {
		if (empty($key)) {
			$this->args[] = $value;
		} else {
			$this->args[$key] = $value;
		}
	}
	public function setArgs(Array $args) {
		$this->args = \array_merge(
			$this->args,
			$args
		);
	}



	public function getTwig($path) {
		return WebRender::Twig(
			$this->globalTwigs,
			$path
		);
	}
	public function getTpl($filename) {
		return WebRender::Tpl(
			$this->globalTwigs,
			$filename
		);
	}
*/



}
