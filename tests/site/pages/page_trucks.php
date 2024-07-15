<?php declare(strict_types=1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2024
 * @license AGPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal\tests\site\pages;


class page_trucks extends \pxn\phpPortal\Page {



	public function getName(): string {
		return 'trucks';
	}



	public function render(): string {
		return 'Trucks Page';
	}



}
