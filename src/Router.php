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
		if (!empty($this->page_current)) return $this->page_current;
		// requested page
		if (!empty($_SERVER['REQUEST_URI'])) {
			$this->page_current = self::san_route($_SERVER['REQUEST_URI']);
			if (!empty($this->page_current)) return $this->page_current;
		}
		// default page
		if (!empty($this->page_default)) {
			$this->page_current = self::san_route($this->page_default);
			if (!empty($this->page_current)) return $this->page_current;
		}
		// 404 page not found
		if (empty($this->page_current))
			$this->page_current = '404';
		return $this->page_current;
	}

	public function getPage(string $path=null): Page {
		if ($this->page_loaded != null)
			return $this->page_loaded;
		if (empty($path)) {
			$path = $this->getCurrentPage();
		}
		$path = StringUtils::trim(text: $path, remove: '/');
		if (empty($path)) throw new \RuntimeException('Failed to find current page');
		$pos = \mb_strpos(haystack: $path, needle: '/');
		$name = null;
		if ($pos === false) {
			$name = $path;
			$path = '';
		} else {
			$name = \mb_substr($path, 0, $pos);
			$path = \mb_substr($path, $pos+1);
		}
		if (!empty($name)) {
			// known page
			if (isset($this->pages[$name])) {
//TODO: probably not needed
//				if ($this->pages[$name] instanceof Page) {
//					$this->page = $this->pages[$name];
//					return $this->page;
//				}
				// load page
				$clss = $this->pages[$name];
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
		// 404 page not found
		if ($name == '404') {
//TODO: logging
			header("HTTP/1.0 404 Not Found");
			echo '404 page itself not found';
			exit(1);
		}
		// get 404 page
		{
			$rt = $this->app->getRouter();
			return $rt->getPage("404/$name/$path");
		}
	}



	public function add(string $name, string|array|Router $page): Router {
		if (empty($name))  throw new RequiredArgumentException('name');
		if ($page == null) throw new RequiredArgumentException('page');
		// add router
		if ($page instanceof Router) {
			return $this->add_router(name: $name, router: $page);
		}
		// add many
		if (\is_array($page)) {
			foreach ($page as $key => $value) {
				$this->add(name: $key, page: $value);
			}
			return $this;
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
	public function add_router(string $name, Router $router=null): Router {
		if (isset($this->routers[$name]))
			if ($router != null && $router !== $this->routers[$name])
				throw new \RuntimeException("Router already registered: $name");
		// new router
		if ($router == null) {
			$router = new Router(app: $this->app, name: $name);
		}
		$this->routers[$name] = $router;
		return $router;
	}
	public function add_page(string $name, string $clss): void {
		if (isset($this->pages[$name]))
			if (!empty($clss) && $clss !== $this->pages[$name])
				throw new \RuntimeException("Page already registered: $name");
		$this->pages[$name] = $clss;
	}



	public function getPagesArray(): array {
		return $this->pages;
	}
	public function getRoutersArray(): array {
		return $this->routers;
	}



	public function def_page(string $name): void {
		$this->page_default = $name;
	}



	public static function san_route(string $route) {
		return \pxn\phpUtils\utils\SanUtils::path_safe(path: $route);
	}



}
