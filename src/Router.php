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
use pxn\phpUtils\utils\SanUtils;
use pxn\phpUtils\exceptions\RequiredArgumentException;
use pxn\phpUtils\exceptions\FileNotFoundException;


class Router {

	protected WebApp $app;
	protected ?Router $parent;

	protected array $routes = [];

	protected ?string $page_current = null;
	protected ?string $page_default = null;
	protected ?Page   $page_loaded  = null;

	public static array $menus = [
		// location
		'main' => [
			// group
			'main' => [],
		],
	];



	public function __construct(WebApp $app, Router $parent=null) {
		$this->app    = $app;
		$this->parent = $parent;
	}



	protected function getCurrentPage(): string {
		if (!empty($this->page_current))
			return $this->page_current;
		// requested page
		if (!empty($_SERVER['REQUEST_URI'])) {
			$this->page_current = self::san_query( $_SERVER['REQUEST_URI'] );
			if (!empty($this->page_current))
				return $this->page_current;
		}
		// default page
		if (!empty($this->page_default)) {
			$this->page_current = self::san_query( $this->page_default );
			if (!empty($this->page_current))
				return $this->page_current;
		}
		// 404 page not found
		$this->page_current = '404';
		return $this->page_current;
	}

	public function getPage(string $query=null): Page {
		// page already loaded
		if ($this->page_loaded != null)
			return $this->page_loaded;
		// requested page
		if (empty($query))
			$query = $this->getCurrentPage();
		$query = self::san_query($query);
		if (empty($query)) throw new \RuntimeException('Failed to find current page');
		$pos = \mb_strpos(haystack: $query, needle: '/');
		$name = null;
		$path = null;
		if ($pos === false) {
			$name = $query;
			$path = '';
		} else {
			$name = \mb_substr($query, 0, $pos);
			$path = \mb_substr($query, $pos+1);
		}
		// known route
		if (!empty($name)) {
			if (isset($this->routes[$name])) {
				$rt = $this->routes[$name];
				// child router
				if ($rt instanceof Router) {
					$this->page_loaded = $rt->getPage($path);
					if ($this->page_loaded != null)
						return $this->page_loaded;
				} else
				// page instance
				if ($rt instanceof Page)
					return $this->page_loaded = $this->routes[$name];
				// load page
				$clss = $rt;
				if (!\class_exists($clss))
					throw new FileNotFoundException("class: $clss");
				return $this->page_loaded = new $clss(app: $this->app, args: $path);
			}
		}
		// 404 page itself not found
		if ($name == '404') {
//TODO: logging
			if (!\headers_sent())
				header("HTTP/1.0 404 Not Found");
			echo "\nError: 404 page itself not found\n";
			exit(1);
		}
		// get 404 page
		return $this->app->getRouter()
				->getPage('404/'.$query);
	}



	public function add(array $pages): void {
		foreach ($pages as $pattern => $entry) {
			// router
			if ($entry instanceof Router) {
				$this->addRouter($pattern, $entry);
				continue;
			}
			// page instance
			if ($entry instanceof Page) {
				$this->addPage(pattern: $pattern, page: $entry);
				continue;
			}
			// page class
			if (\is_string($entry)) {
				$this->addPage(pattern: $pattern, clss: (string)$entry);
				continue;
			}
			// unknown type
			throw new \RuntimeException("Unknown page route type for: $pattern");
		}
	}

	public function addRouter(string $pattern, Router $router=null): Router {
		if (false !== \mb_strpos(haystack: $pattern, needle: '/')) {
			$rt = $this;
			foreach (\explode(string: $pattern, separator: '/') as $part) {
				if (empty($part)) continue;
				$rt = $rt->addRouter(pattern: $part);
			}
			return $rt;
		}
		if (isset($this->routes[$pattern])) {
			if ($router == null)
				return $this->routes[$pattern];
//TODO: logging
//			throw new \RuntimeException("Route pattern already set: $pattern");
		}
		if ($router == null)
			$router = new Router(app: $this->app, parent: $this);
		$this->routes[$pattern] = $router;
		return $router;
	}

	public function addPage(string $pattern, string $clss=null, Page $page=null): void {
//TODO: logging
//		if (isset($this->routes[$pattern]))
//			throw new \RuntimeException("Route pattern already set: $pattern");
		// page instance
		if ($page != null) {
			$this->routes[$pattern] = $page;
		// page class string
		} else
		if (!empty($clss)) {
			$this->routes[$pattern] = $clss;
		} else {
			throw new RequiredArgumentException('clss or page');
		}
	}



	public function getRoutes(): array {
		return $this->routes;
	}


	// default page
	public function defPage(string $name): void {
		$this->page_default = $name;
	}



	public static function san_query(?string $query) {
		if (empty($query))
			return null;
		return
			SanUtils::path_safe(
				StringUtils::trim(text: $query, remove: '/')
			);
	}



}
