<?php declare(strict_types=1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2025
 * @license AGPLv3+ADD-PXN-V1
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal\tests\site\pages;


class page_hammer extends \pxn\phpPortal\Page {



	public function getPageName(): string {
		return 'hammer';
	}
	public function getPageTitle(): string {
		return 'Hammer';
	}



	public function render(): string {
		return 'Hammer Page';
	}

	public function render_api(): ?array {
		return [
			'tool' => 'hammer'
		];
	}



}
