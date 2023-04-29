<?php declare(strict_types=1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2022
 * @license AGPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal;

use \Composer\Autoload\ClassLoader;

use \pxn\phpUtils\utils\GeneralUtils;
use \pxn\phpUtils\utils\StringUtils;
use \pxn\phpUtils\exceptions\RequiredArgumentException;
use \pxn\phpUtils\Debug;


abstract class WebApp extends \pxn\phpUtils\app\xApp {

	public array $args;
	public bool $is_api = false;

	public ?Page $page = null;
	public array $pages = [];

	public array $menus = [];



	public function __construct(?ClassLoader $loader=null) {
		parent::__construct($loader);
		$this->args = GeneralUtils::ParseVarsURI($this);
		if (\count($this->args) > 0
		&& $this->args[0] == "index.php") {
			unset($this->args[0]);
			$this->args = \array_merge($this->args);
		}
		if (\count($this->args) > 0
		&& $this->args[0] == 'api') {
			$this->is_api = true;
			unset($this->args[0]);
			$this->args = \array_merge($this->args);
		}
		if (GeneralUtils::GetVar('api', 'b') === true)
			$this->is_api = true;
	}



	protected function loadPages(): void {
		$this->addPage( new \pxn\phpPortal\pages\page_404($this) );
	}



	public function run(): void {
		// load page
		$this->loadPages();
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
		if ($output !== null && !empty($output))
			echo $output;
	}



	public function getFirstArg(): String {
		if (empty($this->args))
			return '';
		return \reset($this->args);
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
			$found_name = $found->getName();
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



	public function addPage(Page $page): void {
		if ($page == null) throw new RequiredArgumentException('page');
		$this->pages[] = $page;
	}



}
