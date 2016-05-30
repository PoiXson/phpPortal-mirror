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

	protected $website = NULL;



	public function __construct() {
		$this->website = Website::get();
	}



	public abstract function getPageContents();



	public function getTpl($filename) {
		return $this->website->getTpl($filename);
	}



}
