<?php declare(strict_types=1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2024
 * @license AGPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal\tests\site\pages;


class page_hammer extends \pxn\phpPortal\Page {



	public function getPageName(): string {
		return 'hammer';
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
