<?php
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpPortal;

use pxn\phpUtils\System;


abstract class WebRender extends \pxn\phpUtils\app\Render {

//	protected $twigs = array();



	public function doRender() {
		if (System::isShell()) {
			$name = $this->getName();
			fail("Cannot use a WebRender class in this mode! {$name}"); ExitNow(1);
		}
	}



/*
	public function getTwig($path) {
		if (!\is_dir($path)) {
			fail("Template path doesn't exist: {$path}");
			exit(1);
		}
		// existing twig instance
		if (isset($this->twigs[$path]) && $this->twigs[$path] != NULL) {
			return $this->twigs[$path];
		}
		// new twig instance
		$twigLoader = new \Twig_Loader_Filesystem(
			$path
		);
		$options = [
			'debug' => \pxn\phpUtils\debug(),
		];
		if (!\pxn\phpUtils\debug()) {
			$options['cache'] = Paths::getTwigCachePath();
		}
		$twig = new \Twig_Environment(
			$twigLoader,
			$options
		);
		// global vars
		$twig->addGlobal(
			'page',
			[
				'name' => self::$website->getPageName()
			]
		);
//		// load extensions
//		{
//			$extSocial = new Social();
//			$twig->addExtension($extSocial);
//		}
		// ready to use
		$this->twigs[$path] = $twig;
		return $twig;
	}
	public function getTpl($filename) {
		$filename = Strings::ForceEndsWith(
			$filename,
			Defines::TEMPLATE_EXTENSION
		);
		// exact path
		if (\file_exists($filename)) {
			$fileinfo = \pathinfo($filename);
			$twig = $this->getTwig($fileinfo['dirname']);
			$tpl = $twig->loadTemplate($fileinfo['basename']);
			return $tpl;
		}
		// website src/html
		{
			$path = Strings::BuildPath(
				Paths::src(),
				'html'
			);
			if (\file_exists(Strings::BuildPath($path, $filename))) {
				$twig = $this->getTwig($path);
				$tpl = $twig->loadTemplate($filename);
				return $tpl;
			}
		}
		// phpUtils src/html
		{
			$path = Strings::BuildPath(
				Paths::utils(),
				'html'
			);
			if (\file_exists(Strings::BuildPath($path, $filename))) {
				$twig = $this->getTwig($path);
				$tpl = $twig->loadTemplate($filename);
				return $tpl;
			}
		}
		fail("Template file not found: {$filename}");
		return NULL;
	}
*/



}
