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


class Router extends RouteNode {

	protected array $routes = [];

	protected string $page_default = '';

	protected static ?string $request_uri = null;

	protected static bool $is_api = false;

	public static array $menus = [
		// location
		'main' => [
			// group
			'main' => [],
		],
	];



	public function __construct(WebApp $app, ?Router $parent=null, string $key='') {
		parent::__construct(app: $app, parent: $parent, key: $key);
	}



	public function &getRoutesArray(): array {
		return $this->routes;
	}



	public static function isAPI(): bool {
		return self::$is_api;
	}



	// default page
	public function defPage(string $key=null): string {
		if ($key !== null)
			$this->page_default = $key;
		return $this->page_default;
	}

	protected function getRequestURI(): string {
		// cached value
		if (!empty(self::$request_uri))
			return self::$request_uri;
		// requested page
		if (!empty($_SERVER['REQUEST_URI'])) {
			$request = self::san_query($_SERVER['REQUEST_URI']);
			if (!empty($request)) {
				self::$request_uri = $request;
				return self::$request_uri;
			}
		}
		// default page
		if (!empty($this->page_default)) {
			self::$request_uri = $this->page_default;
			return self::$request_uri;
		}
		// 404 page not found
		self::$request_uri = '404';
		return self::$request_uri;
	}



	public function getRouter(string|array $pattern): Router {
		$tokens = [];
		if (empty($pattern))
			return $this;
		if (\is_string($pattern)) {
//TODO: use ? variables
			$pos = \mb_strpos(haystack: $pattern, needle: '?');
			if ($pos !== false) {
				$pattern = \mb_substr($pattern, 0, $pos);
			}
			$tokens =
				\explode(
					'/',
					self::san_query($pattern)
				);
			if (isset($tokens[0]) && $tokens[0] == 'api') {
				unset($tokens[0]);
			}
		} else
		if (\is_array($pattern)) {
			$tokens = $pattern;
		} else {
			throw new \RuntimeException('Unknown pattern type: '.\gettype($pattern));
		}
		if (empty($tokens))
			return $this;
		$tokens = \array_values($tokens);
		$key = '';
		if (isset($tokens[0])) {
			$key = $tokens[0];
			unset($tokens[0]);
		}
		// known route
		if (!empty($key)) {
			if (isset($this->routes[$key])) {
				if (empty($tokens))
					return $this->routes[$key];
				return $this->routes[$key]->getRouter($tokens);
			}
		}
		// new router
		$router = new Router(app: $this->app, parent: $this, key: $key);
		$this->routes[$key] = $router;
		return $router->getRouter($tokens);
	}

	public function getPage(string|array $pattern=null): Page {
		// requested page
		if ($pattern === null) {
			$pattern = $this->getRequestURI();
		}
		if (\is_string($pattern)) {
//TODO: use ? variables
			$pos = \mb_strpos(haystack: $pattern, needle: '?');
			if ($pos !== false) {
				$pattern = \mb_substr($pattern, 0, $pos);
			}
			$tokens =
				\explode(
					'/',
					self::san_query($pattern)
				);
			if (empty($tokens)) throw new \RuntimeException('Failed to find current page');
			$changed = false;
			for ($i=0; $i<count($tokens); $i++) {
				if (empty($tokens[$i])) {
					unset($tokens[$i]);
					$changed = true;
				}
			}
			if ($changed)
				$tokens = \array_values($tokens);
			// api
			if (isset($tokens[0]) && $tokens[0] == 'api') {
				unset($tokens[0]);
				self::$is_api = true;
			}
		} else
		if (\is_array($pattern)) {
			$tokens = $pattern;
		} else {
			throw new \RuntimeException('Unknown pattern type: '.\gettype($pattern));
		}
		$tokens = \array_values($tokens);
		$key = '';
		if (isset($tokens[0])) {
			$key = $tokens[0];
			unset($tokens[0]);
		}
		if (empty($key)) {
			if (!empty($this->page_default)) {
				if (isset($this->routes[$this->page_default])) {
					$route = $this->routes[$this->page_default];
					if ($route instanceof Router) {
						return $route->getPage([]);
					}
					if ($route instanceof PageDAO) {
						$route->setMenuActive();
						return $route->getPageInstance(args: []);
					}
				}
			}
		} else {
		// known route
			if (isset($this->routes[$key])) {
				$route = $this->routes[$key];
				// router
				if ($route instanceof Router) {
					return $route->getPage($tokens);
				}
				// page
				if ($route instanceof PageDAO) {
					$route->setMenuActive();
					return $route->getPageInstance(args: $tokens);
				}
			}
			// 404 page itself not found
			if ($key == '404') {
//TODO: logging
				if (!\headers_sent())
					header("HTTP/1.0 404 Not Found");
				echo "\nError: 404 page itself not found\n";
				exit(1);
			}
		}
		// 404 page not found
		return $this->app->getRouter()
				->getPage('404/'.$this->getRequestURI());
	}



	public function addPage(string|array $pattern): RouteNode {
		$tokens = [];
		if (\is_string($pattern)) {
			$tokens =
				\explode(
					'/',
					StringUtils::trim($pattern, '/')
				);
		} else
		if (\is_array($pattern)) {
			$tokens = $pattern;
		} else {
			throw new \RuntimeException('Unknown pattern type: '.\gettype($pattern));
		}
		if (empty($tokens))
			return $this;
		$tokens = \array_values($tokens);
		$key = $tokens[0];
		unset($tokens[0]);
		$tokens = \array_values($tokens);
		// existing router
		if (isset($this->routes[$key])) {
			$route = $this->routes[$key];
			if (empty($tokens))
				return $route;
			// child router
			if ($route instanceof Router) {
				return $route->addPage($tokens);
			}
			throw new \RuntimeException('Page route already exists: '.$this->getRoutesTree().'/'.$key);
		}
		// new page
		if (empty($tokens)) {
			$dao = new PageDAO(app: $this->app, parent: $this, key: $key);
			$this->routes[$key] = $dao;
			return $dao;
		}
		// new router
		$router = new Router(app: $this->app, parent: $this, key: $key);
		$this->routes[$key] = $router;
		return $router->addPage($tokens);
	}



	public function getRoutesTree(string $key=''): string {
		$tree = [];
		if (!empty($key))
			$tree[] = $key;
		$router = $this;
		while ($router !== null) {
			$key = $router->getKey();
			if (!empty($key))
				$tree[] = $key;
			$router = $router->getParent();
		}
		$tree = \array_reverse($tree, false);
		return \implode('/', $tree);
	}



	public static function san_query(?string $query): string {
		if (empty($query)) return '';
		$query = StringUtils::trim($query, '/');
		if (empty($query)) return '';
		return
			SanUtils::path_safe(
				StringUtils::trim(text: $query, remove: '/')
			);
	}



}
