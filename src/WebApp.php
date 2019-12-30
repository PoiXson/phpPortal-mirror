<?php
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2019
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal;

use pxn\phpUtils\SystemUtils;


abstract class WebApp extends \pxn\phpUtils\app\App {

	protected $page = NULL;
	protected $menu = [];

	protected $render = NULL;



	public function __construct() {
		self::ValidateWeb();
		parent::__construct();
	}



	public function run(): void {
		$render = $this->getRender();
		$render->doRender();
	}



	public function getRender(): \pxn\phpPortal\render\Render {
		if ($this->render == NULL) {
			$this->render = new \pxn\phpPortal\render\Render_Main($this);
		}
		return $this->render;
	}
	public function setRender(\pxn\phpPortal\render\Render $render): void {
		$this->render = $render;
	}



	public function getPage(): \pxn\phpPortal\Page {
		if (empty($this->page)) {
			return $this->getDefaultPage();
		}
		return $this->page;
	}
	public function getPageRendered(): string {
		$page = $this->getPage();
		if (\is_string($page)) {
			
			
			
			
		}
		return $page;
	}
	public abstract function getDefaultPage(): string;



	public static function ValidateWeb(): void {
		if (SystemUtils::isShell()) {
			$name = $this->getName();
			throw new \RuntimeException("This script can only run as web! $name");
		}
	}



}
