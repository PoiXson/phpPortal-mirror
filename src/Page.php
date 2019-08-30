<?php
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2019
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal;


abstract class Page {

	protected $app = NULL;

	protected $pageTitle = NULL;



	public function __construct(\pxn\phpUtils\app\App $app) {
		$this->app = $app;
	}



	public function getPageTitle() {
		return $this->pageTitle;
	}
	public function setPageTitle($title) {
		$this->pageTitle = (
			empty($title)
			? NULL
			: $title
		);
	}
	public function getTitle() {
		return 'TITLE';
	}



	public abstract function getPageContents();



	public function getTpl($filename) {
		return $this->app->getTpl($filename);
	}



}
