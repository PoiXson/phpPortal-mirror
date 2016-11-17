<?php
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpPortal;

use pxn\phpUtils\Defines;
use pxn\phpUtils\Paths;
use pxn\phpUtils\Strings;


abstract class WebRender extends \pxn\phpUtils\app\Render {

	protected $twigs = [];



	public function getTwig($path) {
		return self::Twig(
			$this->twigs,
			$path
		);
	}
	public function getTpl($filename) {
		return self::Tpl(
			$filename
		);
	}
	public static function Twig(&$twigs, $path) {
		if (!\is_dir($path)) {
			fail("Template path doesn't exist: {$path}"); ExitNow(1);
		}
		// existing twig instance
		if (isset($twigs[$path]) && $twigs[$path] != NULL) {
			return $twigs[$path];
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
		$app = \pxn\phpUtils\app\App::get();
		$globalVars = [
			'name' => $app->getPageName()
		];
		$twig->addGlobal('page', $globalVars);
//		// load extensions
//		{
//			$extSocial = new Social();
//			$twig->addExtension($extSocial);
//		}
		// ready to use
		$twigs[$path] = $twig;
		return $twig;
	}
	public static function Tpl(&$twigs, $filename) {
		$filename = Strings::ForceEndsWith(
			$filename,
			Defines::TEMPLATE_EXTENSION
		);
		// exact path
		if (\file_exists($filename)) {
			$fileinfo = \pathinfo($filename);
			$twig = self::Twig($twigs, $fileinfo['dirname']);
			$tpl = $twig->loadTemplate($fileinfo['basename']);
			unset ($fileinfo);
			return $tpl;
		}
		// website src/html
		{
			$path = Strings::BuildPath(
				Paths::src(),
				'html'
			);
			if (\file_exists(Strings::BuildPath($path, $filename))) {
				$twig = self::Twig($twigs, $path);
				$tpl = $twig->loadTemplate($filename);
				return $tpl;
			}
			unset ($path);
		}
		// phpUtils src/html
		{
			$path = Strings::BuildPath(
				Paths::utils(),
				'html'
			);
			if (\file_exists(Strings::BuildPath($path, $filename))) {
				$twig = self::Twig($path);
				$tpl = $twig->loadTemplate($filename);
				return $tpl;
			}
			unset ($path);
		}
		fail("Template file not found: {$filename}");
		return NULL;
	}



}
