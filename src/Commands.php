<?php
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2016
 * @author lorenzo at poixson.com
 * @link http://growcontrol.com/
 */
namespace pxn\phpPortal;

use pxn\phpUtils\ShellTools;


abstract class Commands extends \pxn\phpUtils\app\ShellApp {



	public function doRender() {
		$args = ShellTools::getArgs();
		if (\count($args) >= 1) {
			switch ($args[0]) {

			// export db tables
			case 'export':
echo "\n\n\n<<DO EXPORT>>\n\n\n";
				break;

			// import db tables
			case 'import':
echo "\n\n\n<<DO IMPORT>>\n\n\n";
				break;

			}
		}
		$this->setRendered();
	}



}
