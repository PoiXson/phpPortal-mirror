<?php
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpPortal;

use pxn\phpUtils\Strings;
use pxn\phpUtils\San;
use pxn\phpUtils\System;
use pxn\phpUtils\General;
use pxn\phpUtils\Defines;


abstract class WebApp extends \pxn\phpUtils\app\App {

	const DEFAULT_PAGE_NAME = 'home';

	protected $pageName     = NULL;
	protected $defaultPage  = NULL;
	protected $pageObj      = NULL;
	protected $pageContents = NULL;
	protected $args        = [];
	protected $globalTwigs = [];

	protected $captureBuffer = NULL;



	public function __construct() {
		parent::__construct();
		// default render types
		$this->registerRender( new \pxn\phpPortal\render\RenderWebMain   ($this) );
		$this->registerRender( new \pxn\phpPortal\render\RenderWebSplash ($this) );
		$this->registerRender( new \pxn\phpPortal\render\RenderWebMinimal($this) );
	}
	protected function initApp() {
		parent::initApp();
		// get page name from get/post values
		if (isset($_GET['page']) || isset($_POST['page'])) {
			$page = General::getVar('page', 'str', ['get', 'post']);
			if (!empty($page)) {
				$this->setPageName($page);
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
						$this->setPageName($parts[0]);
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



	// page name
	public function getPageName() {
		if (!empty($this->pageName)) {
			return self::sanPageName( $this->pageName );
		}
		return self::sanPageName(
			$this->getDefaultPage()
		);
	}
	public function setPageName($pageName) {
		if ($this->pageObj != NULL) {
			$pageNameCurrent = $this->pageName;
			fail("Unable to set page to: $pageName  Already set to: $pageNameCurrent",
				Defines::EXIT_CODE_USAGE_ERROR);
		}
		$pageName = self::sanPageName(
			(string) $pageName
		);
		if (!empty($pageName)) {
			$this->pageName = $pageName;
		}
	}
	public function setPage($page) {
		if ($this->pageObj != NULL) {
			$pageName = (
				$page instanceof \pxn\phpPortal\Page
				? $page->getPageTitle()
				: (string) $page
			);
			$pageNameCurrent = $this->pageName;
			fail("Unable to set page to: $pageName  Already set to: $pageNameCurrent",
				Defines::EXIT_CODE_USAGE_ERROR);
		}
		if ($page instanceof \pxn\phpPortal\Page) {
			$this->pageObj = $page;
			$this->pageName = self::sanPageName(
				$page->getName()
			);
		} else {
			$this->setPageName($page);
		}
	}
	public function getDefaultPage() {
		if (!empty($this->defaultPage)) {
			return $this->defaultPage;
		}
		return self::DEFAULT_PAGE_NAME;
	}
	public function setDefaultPage($pageName) {
		$pageName = self::sanPageName($pageName);
		$this->defaultPage = (
			empty($pageName)
			? NULL
			: $pageName
		);
	}



	// load page
	public function getPageObj() {
		// page object already loaded
		if ($this->pageObj != NULL) {
			return $this->pageObj;
		}
		// get page name
		$pageName = $this->getPageName();
		$this->pageName = $pageName;
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
				Defines::KEY_FAILED_PAGE,
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
	public function getPageTitle() {
//TODO: get title from config
		$page = $this->getPageObj();
		if ($page == NULL) {
			return NULL;
		}
		return $page->getPageTitle();
	}
	// called from render class (has internal buffering)
	public function &getPageContents() {
		if (!empty($this->pageContents)) {
			return $this->pageContents;
		}
		$page = $this->getPageObj();
		if ($page != NULL && $page instanceof \pxn\phpPortal\Page) {
			// buffer echo output
			$buffer = &$this->captureBuffer;
			$buffer = '';
			$func = function($buf) use ($buffer) {
				$buffer .= $buf;
			};
			\ob_start($func);
			// get page contents
			$result = $page->getPageContents();
			if ($result !== TRUE && !empty($result)) {
				if ($this->pageContents == NULL) {
					$this->pageContents = '';
				}
				$this->pageContents .= "\n\n\n".$result;
			}
			// end buffering
			\ob_end_flush();
			if (!empty($buffer)) {
				$this->pageContents .= "\n\n\n".$buffer;
			unset($buffer);
			$this->captureBuffer = NULL;
			}
		}
		return $this->pageContents;
	}



	public static function sanPageName($pageName) {
		return San::AlphaNum(
			$pageName
		);
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



}
