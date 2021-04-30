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

	protected string $page_current = '';
	protected string $page_default = '';
	protected ?Page  $page_loaded  = null;

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



	public function getApp(): WebApp {
		return $this->app;
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
		$args = null;
		if ($pos === false) {
			$name = $query;
			$args = '';
		} else {
			$name = \mb_substr($query, 0, $pos);
			$args = \mb_substr($query, $pos+1);
		}
		// known route
		if (!empty($name)) {
			if (isset($this->routes[$name])) {
				$route = $this->routes[$name];
				// child router
				if ($route instanceof Router) {
					$this->page_loaded = $route->getPage($args);
					if ($this->page_loaded != null) return $this->page_loaded;
				}
				// page dao
				if ($route instanceof PageDAO) {
					$this->page_loaded = $route->getPageInstance($args);
					if ($this->page_loaded != null) return $this->page_loaded;
				}
				// page instance
				if ($route instanceof Page) {
					return $this->page_loaded = $this->routes[$name];
				}
				// class string
				if (\is_string($route)) {
					if (!\class_exists($route))
						throw new FileNotFoundException("class: $route");
					$this->page_loaded = new $route(app: $this->app, args: $args);
					if ($this->page_loaded != null) return $this->page_loaded;
				}
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



	public function addRouter(string $name, Router $router=null): Router {
		$name = StringUtils::trim($name, '/');
		if (false !== \mb_strpos(haystack: $name, needle: '/')) {
			$route = $this;
			foreach (\explode(string: $name, separator: '/') as $part) {
				if (empty($part)) continue;
				$route = $route->addRouter(name: $part);
			}
			return $route;
		}
		if (isset($this->routes[$name])) {
			if ($router == null)
				return $this->routes[$name];
			throw new \RuntimeException("Route already set: $name");
		}
		if ($router == null)
			$router = new Router(app: $this->app, parent: $this);
		$this->routes[$name] = $router;
		return $router;
	}

	public function addPage(string $name, PageDAO $dao=null): PageDAO {
		$name = StringUtils::trim($name, '/');
		if (false !== \mb_strpos(haystack: $name, needle: '/')) {
			$route = $this;
			foreach (\explode(string: $name, separator: '/') as $part) {
				if (empty($part)) continue;
				$route = $route->addRouter(name: $part);
			}
			return $route;
		}
		if (isset($this->routes[$name])) {
			if ($this->routes[$name] instanceof PageDAO)
				return $this->routes[$name];
			throw new \RuntimeException("Route already set: $name");
		}
		if ($dao == null) {
			$dao = new PageDAO($this, $name);
		}
		$this->routes[$name] = $dao;
		return $dao;
	}



	public function &getRoutesArray(): array {
		return $this->routes;
	}


	// default page
	public function defPage(string $name=null): string {
		if ($name !== null)
			$this->page_default = $name;
		return $this->page_default;
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
