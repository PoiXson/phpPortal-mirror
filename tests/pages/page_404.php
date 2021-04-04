<?php declare(strict_types = 1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2021
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal\tests\pages;


class page_404 extends \pxn\phpPortal\Page {



	public function render(): void {
		echo '404 Page not found';
	}



}
