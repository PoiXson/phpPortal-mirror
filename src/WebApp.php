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
use \pxn\phpUtils\exceptions\RequiredArgumentException;


abstract class WebApp extends \pxn\phpUtils\app\xApp {

	protected ?\Composer\Autoload\ClassLoader $loader = null;

	public ?string $uri = null;
	public array $args = [];

	public $page = null;
	public array $pages = [];



    public function __construct(?\Composer\Autoload\ClassLoader $loader=NULL) {
		parent::__construct();
		$this->loader = $loader;
	}



	public function loadPages(): void {
		$this->addPage('404', '\\pxn\\phpPortal\\pages\\page_404')
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
		if (\is_string($page)) {
			$page = StringUtils::trim($page, '/');
			if (\str_contains($page, '/')) {
				$this->args = \explode('/', $page);
				$page = \array_shift($this->args);
			}
			// find page
			foreach ($this->pages as $dao) {
				if ($dao->isPageName($page)) {
					$page = $dao;
					break;
				}
			}
		}
		if ($page instanceof \pxn\phpPortal\PageDAO)
			$page = $page->getInstance();
		if ($page instanceof \pxn\phpPortal\Page) {
			if ($page->isValidPage())
				return $page;
			$this->args = [ 'not valid' ];
		}
		// page not found
		{
			$this->args = [];
			if (\is_string($page))
				$this->args[] = $page;
//TODO
//			$this->args = [ print_r($page, true) ];
			$this->page = $this->find404Page()->getInstance();
		}
		return $this->page;
	}
	public function findPage(): string|Page|PageDAO {
		// page already set
		if ($this->page != null) return $this->page;
		// from uri
		if (!empty($this->uri)) {
			if (empty($this->uri))
				return $this->find404Page();
			return $this->uri;
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
		echo "<p>404 NOT FOUND!</p>\n";
		exit(1);
	}

	public function addPage(string $name, string $clss): PageDAO {
		if (empty($name)) throw new RequiredArgumentException('name');
		if (empty($clss)) throw new RequiredArgumentException('clss');
		$dao = new PageDAO($this, $name, $clss);
		$this->addPageDAO($dao);
		return $dao;
	}
	public function addPageDAO(PageDAO $dao): void {
		$this->pages[] = $dao;
	}



}
