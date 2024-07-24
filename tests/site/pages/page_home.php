<?php declare(strict_types=1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2024
 * @license AGPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal\tests\site\pages;


class page_home extends \pxn\phpPortal\Page {



	public function getPageName(): string {
		return 'home';
	}
	public function getPageTitle(): string {
		return 'Home';
	}

	public function isDefaultPage(): bool {
		return true;
	}



	public function render(): string {
		return 'Home Page';
	}



}
