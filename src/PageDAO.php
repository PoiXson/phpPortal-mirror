<?php declare(strict_types = 1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2021
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal;


class PageDAO {

	protected WebApp $app;
	protected Router $router;

	protected string $name;
	protected string $title = '';
	protected string $clss  = '';
	protected string $url   = '';
	protected string $icon  = '';
	protected bool $active = false;



	public function __construct(Router $router, string $name) {
		$this->app = $router->getApp();
		$this->router = $router;
		$this->name = $name;
	}



	public function getTitle(): string {
		return $this->title;
	}
	public function setTitle(string $title): PageDAO {
		$this->title = $title;
		return $this;
	}



	public function getPageClass(): ?string {
		if (empty($this->clss))
			return null;
		return $this->clss;
	}
	public function setPageClass(string $clss): PageDAO {
		$this->clss = $clss;
		return $this;
	}



	public function getURL(): string {
		return $this->url;
	}
	public function setURL(string $url): PageDAO {
		$this->url = $url;
		return $this;
	}



	public function getIcon(): string {
		return $this->icon;
	}
	public function setIcon(string $icon): PageDAO {
		$this->icon = $icon;
		return $this;
	}



	public function isActive(): bool {
		return $this->active;
	}
	public function setActive(?bool $active=null): PageDAO {
		if ($active === null) {
			$this->active = true;
		} else {
			$this->active = $active;
		}
		return $this;
	}



	public function addMenuEntry(string $location=null, string $group=null, string $title=null): PageDAO {
		if (empty($location)) $location = 'main';
		if (empty($group))    $group    = 'main';
		if (empty($title))
			$title = &$this->title;
		else
			$title = "$title";
		Router::$menus[$location][$group][$this->name] = [
			'title'  => &$title,
			'url'    => &$this->url,
			'icls'   => &$this->icon,
			'active' => &$this->active,
		];
//		Router::$menus[$location][$group][$this->name] = $this;
		return $this;
	}



	public function getPageInstance(string $args): Page {
		if (empty($this->clss))
			throw new \RuntimeException('Unknown page class, not set for: '.$this->name);
		if (!\class_exists($this->clss))
			throw new FileNotFoundException('class: '.$this->clss);
		return new $this->clss(app: $this->app, args: $args);
	}



}
