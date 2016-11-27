<?php
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpPortal;

use pxn\phpUtils\San;
use pxn\phpUtils\System;


abstract class WebApp extends \pxn\phpUtils\app\App {

	const DEFAULT_PAGE_NAME = 'home';

	protected $pageName     = NULL;
	protected $defaultPage  = NULL;
	protected $pageObj      = NULL;
	protected $pageContents = NULL;
//	protected $args         = [];
	protected $globalTwigs = [];



	public function __construct() {
		parent::__construct();
		// default render types
		$this->registerRender( new \pxn\phpPortal\render\RenderWebMain   ($this) );
		$this->registerRender( new \pxn\phpPortal\render\RenderWebSplash ($this) );
		$this->registerRender( new \pxn\phpPortal\render\RenderWebMinimal($this) );
	}
//	protected function initArgs() {
//	}



	// app weight
	protected function getWeight() {
		return System::isShell()
			? 0
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
	public function setPage($page) {
		if ($this->pageObj != NULL) {
			$pageName = ($page instanceof Page)
				? $page->getPageTitle()
				: (string) $page;
			fail("Unable to set page to: {$pageName}  Already set to: {$this->pageName}"); ExitNow(1);
		}
		if ($page instanceof Page) {
			$this->pageObj = $page;
			$this->pageName = self::sanPageName(
				$page->getName()
			);
		} else {
			$this->pageName = self::sanPageName(
				(string) $page
			);
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
			fail('Page name could not be detected!'); ExitNow(1);
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
//TODO:
//			$this->args[Defines::KEY_FAILED_PAGE] = $pageName;
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
		if ($page != NULL && $page instanceof Page) {
			// buffer echo output
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
			}
		}
		return $this->pageContents;
	}



	public static function sanPageName($pageName) {
		return San::AlphaNum(
			$pageName
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
