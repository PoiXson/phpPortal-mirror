<?php
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2019
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal\render;

//use pxn\phpPortal\ConfigPortal;

//use pxn\phpUtils\Defines;
//use pxn\phpUtils\System;

use pxn\phpPortal\WebApp;


class Render_Main extends Render {



	public function __construct(WebApp $app) {
		parent::__construct($app);
	}



	public function doRender() {
	}
/*
		if (System::isShell()) {
			$name = $this->getName();
			fail("Cannot use a WebRender class in this mode! $name",
				Defines::EXIT_CODE_USAGE_ERROR);
		}
		if (! $this->app instanceof \pxn\phpPortal\WebApp) {
			fail('App not instance of WebApp!',
				Defines::EXIT_CODE_USAGE_ERROR);
		}
	}
/ *
		// get page contents (has internal buffering)
		$pageContents = $this->app->getPageContents();
		// get page title
		$title = $this->app->getTitle();
		if (\is_callable($title)) {
			$title = $title();
		}
		$title = (string) $title;
		$title = \str_replace(
			'{pagetitle}',
			$title,
			ConfigPortal::getSiteTitle()
		);
		// page icon
		$iconFile  = ConfigPortal::getFavIcon();
		// load global template file
		$tpl = $this->app->getTpl('main');
		// start rendering html
		$CRLF = Defines::CRLF;
		$TAB  = Defines::TAB;

		echo
			'<!DOCTYPE html>'.$CRLF.
			'<html lang="en">'.$CRLF.
			'<head>'.$CRLF.
			'<meta charset="utf-8" />'.$CRLF.
			'<meta http-equiv="X-UA-Compatible" content="IE=edge" />'.$CRLF.
			'<meta name="viewport" content="width=device-width, initial-scale=1" />'.$CRLF.
			"<title>{$title}</title>".$CRLF.

			// fav icon
			(empty($iconFile) ? '' :
				'<link rel="shortcut icon" href="{$iconFile}" type="image/x-icon" />'.$CRLF.
				'<link rel="icon" href="{$iconFile}" type="image/x-icon" />'.$CRLF
			).

			'<link rel="stylesheet" href="/static/main.css" />'.$CRLF.
			'<link rel="stylesheet" href="/static/bootstrap/dist/css/bootstrap.min.css" />'.$CRLF.
			'<script src="/static/jquery/jquery.min.js"></script>'.$CRLF.
			'<script src="/static/bootstrap/dist/js/bootstrap.min.js"></script>'.$CRLF.

//'<meta http-equiv="refresh" content="2" />'.$CRLF.

			'</head>'.$CRLF.
			'<body>'.$CRLF;

		@\ob_flush();

		// render with twig
		echo $tpl->render([
				'PageContents' => &$pageContents
		]);
		echo
			'</body>'.$CRLF.
			'</html>';

		@\ob_flush();
		return TRUE;
	}
/ *
		// shell mode
		if ($isShell) {
			echo "\n";
//			if (!empty($title)) {
//				echo " == Title: $title == \n";
//			}
			echo "\n{$pageContents}\n";
			@\ob_flush();
			return;
		}
*/



}
