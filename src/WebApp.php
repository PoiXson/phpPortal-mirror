<?php
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpPortal;

use pxn\phpUtils\System;


abstract class WebApp extends \pxn\phpUtils\app\App {

//	protected $pageName     = NULL;
//	protected $pageObj      = NULL;
//	protected $pageContents = NULL;
//	protected $args         = [];



	public function __construct() {
		parent::__construct();
		// default render types
		$this->registerRender( new \pxn\phpPortal\render\RenderMain   ($this) );
		$this->registerRender( new \pxn\phpPortal\render\RenderSplash ($this) );
		$this->registerRender( new \pxn\phpPortal\render\RenderMinimal($this) );
	}
//	protected function initArgs() {
//	}



	protected function getWeight() {
		return System::isShell()
			? 0
			: 1000;
	}



}
