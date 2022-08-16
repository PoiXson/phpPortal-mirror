<?php declare(strict_types=1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2022
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal;

use \pxn\phpUtils\utils\StringUtils;


abstract class WebApp extends \pxn\phpUtils\app\xApp {

	protected ?\Composer\Autoload\ClassLoader $loader = null;

	public ?string $uri = null;
//TODO
	public array $args = [];

	public $page = null;
	public array $pages = [];



    public function __construct(?\Composer\Autoload\ClassLoader $loader=NULL) {
		parent::__construct();
		$this->loader = $loader;
	}



	public function loadPages(): void {
		$this->addPage('\\pxn\\phpPortal\\pages\\page_404')
			->set404Page();
	}

	public function run(): void {
		if (empty($this->uri)) {
			$this->uri = (
				isset($_SERVER['REQUEST_URI'])
				? $_SERVER['REQUEST_URI'] : ''
			);
		}
		$this->uri = StringUtils::trim($this->uri, '/');
		// load pages
		$this->loadPages();
		// load page
		$page = $this->getPage();
		if ($page == null)
			throw new \RuntimeException('Failed to find page!');
		// render page
		$page->render();
	}



	public function getPage(): Page {
		$page = $this->page;
		if ($page == null)
			$page = $this->findPage();
		if ($page instanceof \pxn\phpPortal\PageDAO)
			return $page->getInstance();
		if ($page instanceof \pxn\phpPortal\Page)
			return $page;
		throw new \RuntimeException('Unknown page type: '.\get_class($page));
	}
	public function findPage(): string|Page|PageDAO {
		// page already set
		if ($this->page != null) return $this->page;
		// from uri
		if (!empty($this->uri)) {
			$this->page = $this->findPage($this->uri);
			if ($this->page == null)
				return $this->find404Page();
		}
		if ($this->page != null) return $this->page;
		// default page
		$this->page = $this->findDefaultPage();
		if ($this->page != null) return $this->page;
		// 404
		return $this->find404Page();
	}
	public function findDefaultPage(): PageDAO {
		foreach ($this->pages as $page) {
			if ($page->isDefaultPage())
				return $page;
		}
	}
	public function find404Page(): PageDAO {
		foreach ($this->pages as $page) {
			if ($page->is404Page())
				return $page;
		}
//TODO: add 404 page here
		echo "404 Not found!\n";
		exit(1);
	}

	public function addPage(string|PageDAO $page): PageDAO {
		if (\is_string($page)) {
			$page = (string) $page;
			$page = new PageDAO($this, $page);
		}
		if ($page instanceof PageDAO) {
			$this->pages[] = $page;
			return $page;
		} else {
			throw new \RuntimeException('Unknown Page type: '.\get_class($page));
		}
	}



}
