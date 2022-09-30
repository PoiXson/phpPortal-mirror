<?php declare(strict_types=1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2022
 * @license AGPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal;

use \pxn\phpUtils\utils\StringUtils;
use \pxn\phpUtils\exceptions\RequiredArgumentException;


abstract class WebApp extends \pxn\phpUtils\app\xApp {

	protected ?\Composer\Autoload\ClassLoader $loader = null;

//TODO: args not populated
	public ?string $uri = null;
	public array $args = [];

	public ?Page $page = null;
	public array $pages = [];

	public array $menus = [];



	public function __construct(?\Composer\Autoload\ClassLoader $loader=NULL) {
		parent::__construct();
		$this->loader = $loader;
	}



	protected function loadPages(): void {
		$this->addPage( new \pxn\phpPortal\pages\page_404($this) );
	}



	public function run(): void {
		if (empty($this->uri)) {
			$this->uri = (
				isset($_SERVER['REQUEST_URI'])
				? $_SERVER['REQUEST_URI'] : ''
			);
		}
		$this->uri = StringUtils::trim($this->uri, '/');
		// load page
		$this->loadPages();
		$page = $this->getPage();
		if ($page == null)
			throw new \RuntimeException('Failed to find page!');
		// render page
		$page->render();
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
			$weight = $p->isActivePage();
			if ($highest < $weight) {
				$highest = $weight;
				$found = $p;
			}
		}
		if ($found != null)
			return $found;
		return $this->select404Page();
	}
	protected function select404Page(): Page {
		$highest = -1;
		$found = null;
		foreach ($this->pages as $p) {
			if (!$p->is404Page()) continue;
			$weight = $p->isActivePage();
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
