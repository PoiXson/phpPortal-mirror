<?php declare(strict_types=1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2025
 * @license AGPLv3+ADD-PXN-V1
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal\tests\site;

use \pxn\phpUtils\utils\GeneralUtils;


class Website extends \pxn\phpPortal\WebApp {



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



	protected function load_pages(): void {
		parent::load_pages();
		$this->addPage( new \pxn\phpPortal\tests\site\pages\page_home(  $this) );
		$this->addPage( new \pxn\phpPortal\tests\site\pages\page_about( $this) );
		$this->addPage( new \pxn\phpPortal\tests\site\pages\page_cars(  $this) );
		$this->addPage( new \pxn\phpPortal\tests\site\pages\page_hammer($this) );
		$this->addPage( new \pxn\phpPortal\tests\site\pages\page_trucks($this) );
	}



}
