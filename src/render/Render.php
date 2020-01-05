<?php
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2020
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal\render;

//use pxn\phpPortal\DefinesPortal;

//use pxn\phpUtils\Defines;
//use pxn\phpUtils\Paths;
use pxn\phpUtils\Strings;

use pxn\phpPortal\WebApp;


abstract class Render {

	protected $app;

//	protected $twigs = [];

	// css
	protected $cssFiles  = [];
	protected $cssBlocks = [];
	// js
	protected $jsFiles   = [];
	protected $jsBlocks  = [];



	public function __construct(WebApp $app) {
		$this->app = $app;
	}



	public abstract function doRender(): void;



	public function getName(): string {
		$name = \get_called_class();
		$pos = \mb_strrpos($name, '\\');
		if ($pos !== FALSE) {
			$name = \mb_substr($name, $pos+1);
		}
		$name = Strings::TrimFront($name, 'Render', 'Web');
		$name = \mb_strtolower($name);
		return $name;
	}



	protected function renderHeadInsert(): string {
		$output = '';
		// css files
		if (\count($this->cssFiles) > 0) {
			$output .= "<!-- css files -->\n";
			foreach ($this->cssFiles as $file) {
				$output .= "<link rel=\"stylesheet\" href=\"$file\" />\n";
			}
			$output .= "\n";
		}
		// css blocks
		if (\count($this->cssBlocks) > 0) {
			$output .= "<!-- css blocks -->\n";
			foreach ($this->cssBlocks as $block) {
				$output .= "<style>\n$block\n</style>\n";
			}
			$output .= "\n";
		}
		// js files
		if (\count($this->jsFiles) > 0) {
			$output .= "<!-- js files -->\n";
			foreach ($this->jsFiles as $file) {
				$output .= "<script src=\"$file\"></script>\n";
			}
			$output .= "\n";
		}
		// js blocks
		if (\count($this->jsBlocks) > 0) {
			$output .= "<!-- js blocks -->\n";
			foreach ($this->jsBlocks as $block) {
				$output .= "<script>\n$block\n</script>\n";
			}
			$output .= "\n";
		}
		if (empty($output)) {
			return $output;
		}
		return "\n\n$output";
	}



	protected function _html_head_(): string {
		return <<<EOF
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no" />
<meta name="generator" content="Web11">
<title>{title}</title>
{head_insert}
</head>
<body>
EOF;
	}
	protected function _html_foot_(): string {
		return <<<EOF
</body>
</html>
EOF;
	}



/*
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
			fail("Template path doesn't exist: $path",
				Defines::EXIT_CODE_INTERNAL_ERROR);
		}
		// existing twig instance
		if (isset($twigs[$path]) && $twigs[$path] != NULL) {
			return $twigs[$path];
		}
		// new twig instance
		$twigLoader = new \Twig_Loader_Filesystem(
			$path
		);
		$debug = \debug();
		$options = [
			'debug' => $debug,
		];
		if (!$debug) {
			$basePath = Paths::base();
			$options['cache'] = "$basePath/.twig_cache";
		}
		$twig = new \Twig_Environment(
			$twigLoader,
			$options
		);
		// global vars
		$app = \pxn\phpUtils\app\App::get();
		$globalVars = [
			'name' => ConfigPortal::getPageName()
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
			DefinesPortal::TEMPLATE_EXTENSION
		);
		// exact path
		if (\file_exists($filename)) {
			$fileinfo = \pathinfo($filename);
			$twig = self::Twig($twigs, $fileinfo['dirname']);
			$tpl = $twig->loadTemplate($fileinfo['basename']);
			unset($fileinfo);
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
			unset($path);
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
			unset($path);
		}
		fail("Template file not found: $filename",
			Defines::EXIT_CODE_INTERNAL_ERROR);
	}
*/



}
