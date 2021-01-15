<?php declare(strict_types = 1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2021
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal\render;

//use pxn\phpUtils\Defines;
//use pxn\phpUtils\System;

use pxn\phpUtils\app\App;


class Render_Minimal extends Render {



	public function __construct(App $app) {
		parent::__construct($app);
	}



/*
//	public function getName() {
//		return 'minimal';
//	}
//	public function getWeight() {
//		return 10;
//	}



	public function doRender() {
//		if (System::isShell()) {
//			$name = $this->getName();
//			fail("Cannot use a WebRender class in this mode! $name",
//				Defines::EXIT_CODE_USAGE_ERROR);
//		}
fail('UNFINISHED!');
//		return TRUE;
	}
*/



}
