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
		parent::__construct();
		if (SystemUtils::isShell()) {
			throw new \RuntimeException('Cannot use a WebRender class in this mode: '.\get_called_class());
		}
	}



	public function run() {
		$render = $this->getRender();
		$render->doRender();
	}



	public function getRender() {
		if ($this->render == NULL) {
			$this->render = new \pxn\phpPortal\render\Render_Main($this);
		}
		return $this->render;
	}
	public function setRender(\pxn\phpUtils\app\Render $render) {
		$this->render = $render;
	}



	public function getPage() {
		if (empty($this->page)) {
			return $this->getDefaultPage();
		}
		return $this->page;
	}
	public function getPageRendered() {
		$page = $this->getPage();
		if (\is_string($page)) {
			
			
			
			
		}
		return $page;
	}
	public abstract function getDefaultPage();



}
