<?php declare(strict_types=1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2025
 * @license AGPLv3+ADD-PXN-V1
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal\tests\site\pages;


class page_trucks extends \pxn\phpPortal\Page {



	public function getPageName(): string {
		return 'trucks';
	}
	public function getPageTitle(): string {
		return 'Trucks';
	}



	public function render(): string {
		return 'Trucks Page';
	}



}
