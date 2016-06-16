<?php
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpPortal;

use pxn\phpUtils\Config;
use pxn\phpUtils\Paths;
use pxn\phpUtils\Strings;
use pxn\phpUtils\San;
use pxn\phpUtils\General;
use pxn\phpUtils\System;
use pxn\phpUtils\Defines;


abstract class Website {

	protected static $instance = NULL;
	protected static $siteNamespace   = NULL;
	//protected static $portalNamespace = NULL;

	protected $render = NULL;
	protected $hasRendered = FALSE;

	protected $pageName     = NULL;
	protected $pageObj      = NULL;
	protected $pageContents = NULL;
	protected $args         = [];



	public static function get() {
//		if(self::$instance == NULL)
//			self::$instance = new self();
		return self::$instance;
	}
	public static function peak() {
		return self::$instance;
	}
	public function __construct() {
		if (self::$instance != NULL) {
			fail('Website instance already started!');
			exit(1);
		}
		self::$instance = $this;
		// load db configs
		{
			$path = Paths::base();
			// search for .htdb files
			$array = \scandir($path);
			foreach ($array as $f) {
				if ($f == '.' || $f == '..')
					continue;
				if (!Strings::EndsWith($f, '.php'))
					continue;
				if (!Strings::StartsWith($f, '.htdb.'))
					continue;
				include(Strings::BuildPath($path, $f));
			}
		}
		// shell args
		if (System::isShell()) {
			global $argv;
			if (\array_key_exists(0, $argv)) {
				unset($argv[0]);
			}
			if (\count($argv) > 0) {
				for ($i=1; $i<\count($argv)+1; $i++) {
					$arg = $argv[$i];
					// starts with --
					if (Strings::StartsWith($arg, '--')) {
						if (\count($argv) <= $i) {
							fail("value missing from argument: {$arg}");
							exit(1);
						}
						// duplicate arg
						if (\array_key_exists($arg, $this->args)) {
							// convert to array
							if (!\is_array($this->args[$arg])) {
								$this->args[$arg] = [
									$this->args[$arg]
								];
							}
							$this->args[$arg][] = $argv[++$i];
						} else {
							$this->args[$arg] = $argv[++$i];
						}
					} else
					// starts with -
					if (Strings::StartsWith($arg, '-')) {
						$this->args[$arg] = TRUE;
					} else
					// page argument
					if ($i == 1) {
						$this->setPageName($arg);
					// plain string
					} else {
						$this->args[] = $arg;
					}
				}
			}
		// web args
		} else {
			// get page name from get/post values
			if (isset($_GET['page']) || isset($_POST['page'])) {
				$pageName = General::getVar(
					'page',
					'str',
					['get', 'post']
				);
				if (!empty($pageName)) {
					$this->setPageName($pageName);
				}
			}
			// get page name from url path
			if ($this->pageName === NULL && isset($_SERVER['REQUEST_URI'])) {
				$urlPath = Strings::Trim($_SERVER['REQUEST_URI'], ' ', '/');
				if (!empty($urlPath)) {
					$args = \explode('/', $urlPath);
					if (count($args) >= 1 && !empty($args[0])) {
						$this->pageName = $args[0];
						unset($args[0]);
						$this->args = \array_values($args);
					}
				}
			}
		}
		// init render handler
		$this->getRender();
		// render at shutdown
		\register_shutdown_function([$this, 'shutdown']);
	}



	public function setIcon($iconfile) {
		Config::set('icon', $iconfile);
	}



	public function getRender() {
		if ($this->render == NULL) {
			$renderType = \pxn\phpUtils\Config::getRenderType();
			switch ($renderType) {
			case 'main':
				$this->render = new RenderMain();
				break;
			case 'splash':
				$this->render = new RenderSplash();
				break;
			case 'minimal':
				$this->render = new RenderMinimal();
				break;
			default:
				\fail ("Unknown render type: {$type}");
				exit(1);
			}
		}
		return $this->render;
	}
	public function doRender() {
		$this->setRendered();
		$render = $this->getRender();
		//echo $render->doRender();
		$render->doRender();
	}
	public function shutdown() {
		if ($this->hasRendered()) {
			return;
		}
		$this->doRender();
	}



	public function hasRendered() {
		return $this->hasRendered;
	}
	public function setRendered($value=NULL) {
		if ($value === NULL) {
			$value = TRUE;
		}
		$this->hasRendered = ($value == TRUE);
	}



	public function getTwig() {
		$render = $this->getRender();
		return $render->getTwig();
	}
	public function getTpl($filename) {
		$render = $this->getRender();
		return $render->getTpl($filename);
	}



	public function getPageName() {
		if ($this->pageName != NULL) {
			return San::AlphaNum($this->pageName);
		}
		return San::AlphaNum($this->getDefaultPage());
	}
	public function getPageObj() {
		if ($this->pageObj != NULL) {
			return $this->pageObj;
		}
		$pageName = $this->getPageName();
		$this->pageName = $pageName;
		if (empty($pageName)) {
			fail('pageName value could not be found!');
			exit(1);
		}
		// website page class
		{
			$namespace = self::getSiteNamespace();
			$clss = "{$namespace}\\pages\\page_{$pageName}";
			if (\class_exists($clss, TRUE)) {
				$this->pageObj = new $clss();
				return $this->pageObj;
			}
		}
		// internal page class
		{
			$clss = "\\pxn\\phpPortal\\pages\\page_{$pageName}";
			if (\class_exists($clss, TRUE)) {
				$this->pageObj = new $clss();
				return $this->pageObj;
			}
		}
		// return 404 page
		if ($pageName != '404') {
			\http_response_code(404);
			$this->args[Defines::KEY_FAILED_PAGE] = $pageName;
			$this->pageName = '404';
			$this->pageObj = $this->getPageContents();
			return $this->pageObj;
		}
		// 404 page not found
		$this->pageObj = NULL;
		$this->pageContents = "\n<h1>404 - Page Not Found!</h1>\n\n";
	}
	public function getPageContents() {
		if (!empty($this->pageContents)) {
			return $this->pageContents;
		}
		$pageObj = $this->getPageObj();
		if ($pageObj != NULL && !\is_string($pageObj)) {
			$this->pageContents = $pageObj->getPageContents();
		}
		return $this->pageContents;
	}



	public static function getSiteNamespace() {
		if (self::$siteNamespace != NULL) {
			return self::$siteNamespace;
		}
		$reflect = new \ReflectionClass(self::get());
		$clss = $reflect->getName();
		unset($reflect);
		$pos = \strrpos($clss, '\\');
		if ($pos === FALSE || $pos < 3) {
			fail("Invalid website class namespace: {$clss}");
			exit(1);
		}
		$namespace = \substr($clss, 0, $pos);
		$namespace = Strings::ForceStartsWith($namespace, '\\');
		self::$siteNamespace = $namespace;
		return $namespace;
	}
	public static function getPortalNamespace() {
		return '\\pxn\\phpPortal';
//		if (Self::$portalNamespace != NULL) {
//			return self::$portalNamespace;
//		}
//		$clss = __CLASS__;
//		$pos = \strrpos($clss, '\\');
//		if ($pos === FALSE || $pos < 3) {
//			fail("Invalid website class namespace: {$clss}");
//			exit(1);
//		}
//		$namespace = \substr($clss, 0, $pos);
//		$namespace = Strings::ForceStartsWith($namespace, '\\');
//		self::$portalNamespace = $namespace;
//		return $namespace;
	}



	public function getDefaultPage() {
		return 'home';
	}
	public function setPageName($pageName) {
		$this->pageName = $pageName;
	}



	public function getArg($name, $default=NULL) {
		if (isset($this->args[$name])) {
			return $this->args[$name];
		}
		return $default;
	}
	public function getArgs() {
		return $this->args;
	}



}
