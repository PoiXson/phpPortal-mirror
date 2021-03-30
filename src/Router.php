<?php declare(strict_types = 1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2021
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal;

use pxn\phpUtils\utils\StringUtils;
use pxn\phpUtils\exceptions\RequiredArgumentException;
use pxn\phpUtils\exceptions\FileNotFoundException;


class Router {

	protected WebApp $app;

	protected ?string $name;

	protected array $pages   = [];
	protected array $routers = [];

	protected ?string $page_current = null;
	protected ?string $page_default = null;
	protected ?Page   $page_loaded  = null;



	public function __construct(WebApp $app, string $name=null) {
		$this->app  = $app;
		$this->name = $name;
	}



	public function getCurrentPage(): string {
		if (!empty($this->page_current))
			return $this->page_current;
		// requested page
		if (!empty($_SERVER['REQUEST_URI'])) {
			$query = self::san_query( $_SERVER['REQUEST_URI'] );
			if (!empty($query)) { $this->page_current = $query; return $query; }
		}
		// default page
		if (!empty($this->page_default)) {
			$query = self::san_query( $this->page_default );
			if (!empty($query)) { $this->page_current = $query; return $query; }
		}
		// 404 page not found
		$this->page_current = '404';
		return $this->page_current;
	}

	public function getPage(string $query=null): Page {
		// page already loaded
		if ($this->page_loaded != null)
			return $this->page_loaded;
		$query = self::san_query($query);
		// current page
		if (empty($query))
			$query = $this->getCurrentPage();
		if (empty($query)) throw new \RuntimeException('Failed to find current page');
		$pos = \mb_strpos(haystack: $query, needle: '/');
		$name = null;
		if ($pos === false) {
			$name = $query;
			$path = '';
		} else {
			$name = \mb_substr($query, 0, $pos);
			$path = \mb_substr($query, $pos+1);
		}
		if (!empty($name)) {
			// known page
			if (isset($this->pages[$name])) {
				// probably not needed but just in case
				if ($this->pages[$name] instanceof Page) {
					$this->page = $this->pages[$name];
					return $this->page;
				}
				// load page
				$clss = $this->pages[$name];
				if (!\class_exists($clss))
					throw new FileNotFoundException("class: $clss");
				$this->page = new $clss(app: $this->app, args: $path);
				return $this->page;
			}
			// child router
			if (isset($this->routers[$name])) {
				if (empty($path))
					$path = $this->routers[$name]->page_default;
					
					
					
				return $this->routers[$name]->getPage($path);
			}
		}
		// 404 page itself not found
		if ($name == '404') {
//TODO: logging
			header("HTTP/1.0 404 Not Found");
			echo '404 page itself not found';
			exit(1);
		}
		// get 404 page
		{
			$router = $this->app->getRouter();
			return $router->getPage("404/$query");
		}
	}



	public function addMany(array $pages): Router {
		foreach ($pages as $key => $value) {
			$this->add(name: $key, page: $value);
		}
		return $this;
	}
	public function add(string $name, string|Router $page): Router {
		if (empty($name))  throw new RequiredArgumentException('name');
		if ($page == null) throw new RequiredArgumentException('page');
		// add router
		if ($page instanceof Router) {
			return $this->add_router(name: $name, router: $page);
		}
		// add page class
		if (\is_string($page)) {
			$page = (string) $page;
			if (empty($page)) throw new RequiredArgumentException('page');
			$this->add_page(name: $name, clss: $page);
			return $this;
		}
		// unknown type
		throw new \RuntimeException("Unknown page type for: $name");
	}
	public function router(string $name): Router {
		if (empty($name)) throw new \RequiredArgumentException('router name');
		if (!isset($this->routers[$name])) {
			$this->routers[$name] = new self(app: $this->app, name: $name);
		}
		return $this->routers[$name];
	}
	public function add_router(string $name, Router $router): void {
		if (isset($this->routers[$name]))
			if ($router !== $this->routers[$name])
				throw new \RuntimeException("Router already registered: $name");
		$this->routers[$name] = $router;
	}
	public function add_page(string $name, string $clss): void {
		if (isset($this->pages[$name]))
			if (!empty($clss) && $clss !== $this->pages[$name])
				throw new \RuntimeException("Page already registered: $name");
		$this->pages[$name] = $clss;
	}



	public function get_pages_array(): array {
		return $this->pages;
	}
	public function get_routers_array(): array {
		return $this->routers;
	}



	// default page
	public function defpage(string $name): void {
		$this->page_default = $name;
	}



	public static function san_query(?string $query) {
		if (empty($query))
			return null;
		return
			\pxn\phpUtils\utils\SanUtils::path_safe(
				path: StringUtils::trim(text: $query, remove: '/')
			);
	}



}
