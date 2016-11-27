<?php
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2016
 * @author lorenzo at poixson.com
 * @link http://growcontrol.com/
 * /
namespace pxn\phpPortal;

use pxn\phpUtils\ShellTools;


abstract class Commands extends \pxn\phpUtils\app\ShellApp {



	protected function doRender() {
		$args = ShellTools::getArgs();
		// one or more args
		if (\count($args) >= 1) {
			$arg = \strtolower($args[0]);
			switch ($arg) {

			// export db tables
			case 'export':
echo "\n\n\n<<DO EXPORT>>\n\n\n";
				break;

			// import db tables
			case 'import':
echo "\n\n\n<<DO IMPORT>>\n\n\n";
				break;

			// unknown arg
			default:
				return FALSE;
			}
			$this->setRendered();
			return TRUE;
		}
		return FALSE;
	}



}
*/