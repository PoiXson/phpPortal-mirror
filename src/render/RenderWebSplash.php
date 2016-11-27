<?php
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpPortal\render;

use pxn\phpUtils\System;


class RenderWebSplash extends \pxn\phpPortal\WebRender {



	public function getName() {
		return 'splash';
	}
	public function getWeight() {
		return 1;
	}



	public function doRender() {
		if (System::isShell()) {
			$name = $this->getName();
			fail("Cannot use a WebRender class in this mode! {$name}"); ExitNow(1);
		}
fail('UNFINISHED!'); ExitNow(1);
	}



}
