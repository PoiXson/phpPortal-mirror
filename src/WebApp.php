<?php declare(strict_types = 1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2021
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

	protected $autoload = NULL;

	protected $page = NULL;
	protected $pageDefault = NULL;

	protected $menus = [];
	protected $pages = [];

	protected $render = NULL;



	public function __construct(?\Composer\Autoload\ClassLoader $autoload=NULL) {
		if ($autoload != NULL) {
			$this->autoload = $autoload;
		}
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
		$this->loadPage();
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



	public function getTitle(): string {
		$this->loadPage();
		if ($this->page instanceof Page) {
			$title = $this->page->getPageTitle();
			if (!empty($title)) {
				return $title;
			}
		}
//TODO: default site title
		return '';
	}



	public function getPage() {
		if (empty($this->page)) {
			return $this->getDefaultPage();
		}
		return $this->page;
	}
	public function getPageName() {
		if ($this->page instanceof Page) {
			return $this->page->getName();
		} else
		if (\is_string($this->page)) {
			return (string) $this->page;
		}
		return NULL;
	}
	public function loadPage(): void {
		$page = $this->getPage();
		// search for page
		if (\is_string($page)) {
			$page = San::AlphaNum($page);
			if (isset($this->pages[$page])) {
				$pageClass = $this->pages[$page];
				if (\class_exists($pageClass)) {
					$this->page = new $pageClass($this);
					return;
				}
			}
			// page not found
			$this->page = new \pxn\phpPortal\pages\page_404($this, $page);
		}
	}
	public function getPageContents(): string {
		$this->loadPage();
		// page object
		if ($this->page instanceof Page) {
			return $this->page->getContents();
		}
		// page not found
		$this->page = new \pxn\phpPortal\pages\page_404($this, $this->getPage());
		return $this->page->getContents();
	}
	public function getDefaultPage(): ?string {
		return $this->pageDefault;
	}



	public static function AssertWeb(): void {
		if (SystemUtils::isShell()) {
			$name = $this->getName();
			throw new \RuntimeException("This script can only run as web! $name");
		}
	}



	public function &getMenusArray(?string $group=NULL): array {
		if (empty($group))
			return $this->menus;
		if (!isset($this->menus[$group]))
			$this->menus[$group] = [];
		return $this->menus[$group];
	}
	public function &getPagesArray(): array {
		return $this->pages;
	}



}
