<?php
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2019
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal;


abstract class WebApp extends \pxn\phpUtils\app\App {

	protected $menu = [];



	public function __construct() {
		parent::__construct();
	}



	public function run() {
echo '<p>RENDER</p>';
	}



}
