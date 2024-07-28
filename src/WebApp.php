<?php declare(strict_types=1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2024
 * @license AGPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal;

use \Composer\Autoload\ClassLoader;

use \pxn\phpUtils\utils\GeneralUtils;
use \pxn\phpUtils\utils\StringUtils;
use \pxn\phpUtils\utils\SystemUtils;
use \pxn\phpUtils\exceptions\RequiredArgumentException;
use \pxn\phpUtils\Debug;


abstract class WebApp extends \pxn\phpUtils\app\xApp {

	protected bool $init_session = false;

	public array $args = [];
	public bool $is_api = false;

	public ?Page $page = null;
	public array $pages = [];

	public array $menus = [];



	public function __construct(?ClassLoader $loader=null) {
		$this->assert_is_web();
		parent::__construct($loader);
		$this->parseURI();
	}



	protected function parseURI(?string $uri=null): void {
		if (empty($uri)) $uri = @$_SERVER['REQUEST_URI'];
		if (empty($uri)) $uri = '';
		$this->args = GeneralUtils::ParseVarsURI($uri);
		if (\count($this->args) > 0) {
			switch ($this->args[0]) {
				case 'index.php':
					unset($this->args[0]);
					$this->args = \array_merge($this->args);
					break;
				case 'api':
					$this->is_api = true;
					unset($this->args[0]);
					$this->args = \array_merge($this->args);
					break;
				default: break;
			}
		}
		if (\GetVar(name: 'api', type: 'b', src: 'gp') === true)
			$this->is_api = true;
	}



	protected function load_pages(): void {
		$this->addPage( new \pxn\phpPortal\pages\page_404($this) );
	}

	public function addPage(Page $page): void {
		if ($page == null) throw new RequiredArgumentException('page');
		$name = $page->getPageName();
		if (empty($name)) $this->pages[]      = $page;
		else              $this->pages[$name] = $page;
	}



	public function run(): void {
		// load page
		$this->load_pages();
		$page = $this->getPage();
		if ($page == null)
			throw new \RuntimeException('Failed to find page!');
		// render page
		$output = null;
		if ($this->is_api) {
			$json = $page->renderAPI();
			$flags = (Debug::debug() ? \JSON_PRETTY_PRINT : 0);
			$output = \json_encode($json, $flags);
		} else {
			$output = $page->render();
		}
		if (!empty($output))
			echo $output;
		$this->doExit();
	}
	public function doExit(): void {
		\session_write_close();
	}



	public function getFirstArg(): String {
		if (empty($this->args))
			return '';
		return \reset($this->args);
	}
	public function getArgs(): array {
		return $this->args;
	}



	public function getPage(): Page {
		if ($this->page != null) return $this->page;
		$this->page = $this->selectPage();
		if ($this->page != null) return $this->page;
		// page not found
		$this->page = $this->select404Page();
		if ($this->page != null) return $this->page;
		throw new \RuntimeException('Unable to find a page');
	}
	protected function selectPage(): Page {
		$highest = -1;
		$found = null;
		foreach ($this->pages as $p) {
			$weight = $p->getActiveWeight();
			if ($highest < $weight) {
				$highest = $weight;
				$found = $p;
			}
		}
		if ($found != null) {
			$found_name = $found->getPageName();
			foreach ($this->menus as $grp_name => $group) {
				if (!\is_array($group)) continue;
				foreach ($group as $name => $menu) {
					if (!\is_array($menu)) continue;
					if ($found_name == $name)
						$this->menus[$grp_name][$name]['active'] = true;
				}
			}
			return $found;
		}
		return $this->select404Page();
	}
	protected function select404Page(): Page {
		$highest = -1;
		$found = null;
		foreach ($this->pages as $p) {
			if (!$p->is404Page()) continue;
			$weight = (
				$p->isDefaultPage()
				? Page::DEFAULT_PAGE_WEIGHT
				: $p->getActiveWeight()
			);
			if ($highest <= $weight) {
				$highest = $weight;
				$found = $p;
			}
		}
	}



	public function assert_is_web(): void {
		if (SystemUtils::IsShell())
			throw new \RuntimeException('This script can only run as a website');
	}



	public function initSession(): void {
		if ($this->init_session) return;
		$this->init_session = true;
		\session_name($this->getSessionName());
		\session_start();
	}
	public function getSessionName(): ?string {
		return null;
	}



}
