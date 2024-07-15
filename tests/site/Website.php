<?php declare(strict_types=1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2024
 * @license AGPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal\tests\site;

use \pxn\phpUtils\utils\GeneralUtils;


class Website extends \pxn\phpPortal\WebApp {

	public bool $has_checked_run_state = false;



	public function __construct() {
		parent::__construct();
	}



	public function updateURI(string $uri): void {
		$this->page   = null;
		$this->is_api = false;
		$this->pages  = [];
		$this->args   = [];
		$this->parseURI($uri);
	}



	protected function check_run_mode(): void {
		parent::check_run_mode();
		$this->has_checked_run_state = true;
	}



	protected function load_pages(): void {
		parent::load_pages();
		$this->addPage( new \pxn\phpPortal\tests\site\pages\page_home(  $this) );
		$this->addPage( new \pxn\phpPortal\tests\site\pages\page_about( $this) );
		$this->addPage( new \pxn\phpPortal\tests\site\pages\page_cars(  $this) );
		$this->addPage( new \pxn\phpPortal\tests\site\pages\page_hammer($this) );
		$this->addPage( new \pxn\phpPortal\tests\site\pages\page_trucks($this) );
	}



}
