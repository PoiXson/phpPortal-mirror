<?php
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpPortal;


abstract class Page {

	protected $app = NULL;



	public function __construct(\pxn\phpUtils\app\App $app) {
		$this->app = $app;
	}



	public abstract function getPageTitle();
	public abstract function getPageContents();



	public function getTpl($filename) {
		return $this->app->getTpl($filename);
	}



}
