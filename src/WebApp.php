<?php
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2020
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal;

use pxn\phpUtils\GeneralUtils;
use pxn\phpUtils\SystemUtils;
use pxn\phpUtils\Strings;
use pxn\phpUtils\San;


abstract class WebApp extends \pxn\phpUtils\app\App {

	protected $page = NULL;

	protected $menus = [];
	protected $pages = [];

	protected $render = NULL;



	public function __construct() {
		self::AssertWeb();
		parent::__construct();
		{
			$page = GeneralUtils::getVar('page', 's', 'g', 'p');
			if (empty($page)) {
				if (isset($_SERVER['REQUEST_URI'])) {
					$uri = $_SERVER['REQUEST_URI'];
					$uri = Strings::Trim($uri, '/', ' ');
					$parts = \explode('/', $uri, 2);
					$page = $parts[0];
				}
			}
			if (!empty($page)) {
				$this->page = San::AlphaNum($page);
			}
		}
	}



	public function run(): void {
		$render = $this->getRender();
		$render->doRender();
	}



	public function getRender(): \pxn\phpPortal\render\Render {
		if ($this->render == NULL) {
			$this->render = new \pxn\phpPortal\render\Render_Main($this);
		}
		return $this->render;
	}
	public function setRender(\pxn\phpPortal\render\Render $render): void {
		$this->render = $render;
	}



	public function getPage() {
		if (empty($this->page)) {
			return $this->getDefaultPage();
		}
		return $this->page;
	}
	public function getPageRendered(): string {
		$page = $this->getPage();
		// search for page
		if (\is_string($page)) {
			$page = San::AlphaNum($page);
			if (isset($this->pages[$page])) {
				$pageClass = $this->pages[$page];
				if (\class_exists($pageClass)) {
					$this->page = new $pageClass($this);
					return $this->page->getContents();
				}
			}
			// page not found
			$page = new \pxn\phpPortal\pages\page_404($this);
			return $page->getContents();
		}
		// page object
		if ($page instanceof \pxn\phpPortal\Page) {
			return $page->getContents();
		}
		// page not found
		$page = new \pxn\phpPortal\pages\page_404($this);
		return $page->getContents();
	}
	public abstract function getDefaultPage(): string;



	public static function AssertWeb(): void {
		if (SystemUtils::isShell()) {
			$name = $this->getName();
			throw new \RuntimeException("This script can only run as web! $name");
		}
	}



	public function &getMenuArray(?string $group=NULL): array {
		if (empty($group))
			return $this->menu;
		if (!isset($this->menu[$group]))
			$this->menu[$group] = [];
		return $this->menu[$group];
	}
	public function &getPagesArray(): array {
		return $this->pages;
	}
	public function addPage(string $name, string $classPath): void {
		$this->pages[$name] = $classPath;
	}



}
