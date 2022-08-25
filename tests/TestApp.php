<?php declare(strict_types=1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2022
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal\tests;

use \pxn\phpPortal\WebApp;


class TestApp extends WebApp {

	public bool $has_checked_run_state = false;



	public function __construct() {
		parent::__construct();
	}



	protected function check_run_mode(): void {
		$this->has_checked_run_state = true;
	}



	public function run(): void {
	}



	protected function load_pages(): void {
	}



}
