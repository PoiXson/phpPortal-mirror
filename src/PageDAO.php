<?php declare(strict_types=1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2022
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal;


class PageDAO {

	public ?WebApp $app = null;

	public ?string $clss = null;
	public ?Page $instance = null;

	public bool $isDefault = false;
	public bool $is404     = false;



	public function __construct(WebApp $app, string $clss) {
		$this->app = $app;
		$this->clss = $clss;
		if (!\class_exists($clss))
			throw new \RuntimeException('Page class not found: '.$clss);
	}



	public function getInstance(): Page {
		if ($this->instance == null)
			$this->instance = new $this->clss($this->app);
		return $this->instance;
	}


	public function isDefaultPage(): bool {
		return $this->isDefault;
	}
	public function setDefaultPage(?bool $isDefault=null): self {
		if ($isDefault === null) {
			$this->isDefault = true;
		} else {
			$this->isDefault = (bool) $isDefault;
		}
		return $this;
	}



	public function is404Page(): bool {
		return $this->is404;
	}
	public function set404Page(?bool $is404=null): self {
		if ($is404 === null) {
			$this->is404 = true;
		} else {
			$this->is404 = (bool) $is404;
		}
		return $this;
	}



}
