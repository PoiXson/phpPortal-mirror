<?php declare(strict_types=1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2022
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 * /
namespace pxn\phpPortal;

use pxn\phpUtils\exceptions\FileNotFoundException;


class PageDAO extends RouteNode {

	protected string $title = '';
	protected string $clss  = '';
	protected string $url   = '';
	protected string $icon  = '';

	protected ?Page $page_loaded = null;
	protected bool $menu_active = false;



	public function __construct(WebApp $app, Router $parent, string $key='') {
		parent::__construct(app: $app, parent: $parent, key: $key);
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



	public function isMenuActive(): bool {
		return $this->menu_active;
	}
	public function setMenuActive(?bool $active=null): PageDAO {
		if ($active === null) {
			$this->menu_active = true;
		} else {
			$this->menu_active = $active;
		}
		return $this;
	}



	public function getPageInstance(array $args=[]): Page {
		if ($this->page_loaded == null) {
			$clss = $this->clss;
			if (empty($clss))
				throw new \RuntimeException('Unknown page class, not set for: '.$this->key);
			if (!\class_exists($clss))
				throw new FileNotFoundException('class: '.$clss);
			$this->page_loaded =
				new $clss(
					app: $this->app,
					key: $this->key
				);
			if (!empty($args))
				$this->page_loaded->setArgs($args);
		}
		return $this->page_loaded;
	}



	public function addMenuEntry(string $location=null, string $group=null, string $title=null): PageDAO {
		if (empty($location)) $location = 'main';
		if (empty($group))    $group    = 'main';
		$entry = [];
		if (empty($title)) {
			$entry['title'] = &$this->title;
		} else {
			$entry['title'] = $title;
		}
		if (empty($this->url)) {
			$entry['url'] = '/'.$this->parent->getRoutesTree($this->key);
		} else {
			$entry['url'] = &$this->url;
		}
		$entry['icls']   = &$this->icon;
		$entry['active'] = &$this->menu_active;
		Router::$menus[$location][$group][$this->key] = $entry;
		return $this;
	}



}
*/
