<?php declare(strict_types = 1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2021
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal\render;

//use pxn\phpPortal\ConfigPortal;

//use pxn\phpUtils\Defines;
use pxn\phpUtils\SystemUtils;

use pxn\phpPortal\WebApp;


class Render_Main extends Render {



	public function __construct(WebApp $app) {
		parent::__construct($app);
	}



	public function doRender(): void {
		if (!empty($this->forwardTo)) {
			if (\headers_sent()) {
				echo <<<EOF
<html><header><meta http-equiv="refresh" content="0;url={$this->forwardTo}"></header>
<body><p><a href="{$this->forwardTo}"><font size="+1">Continue..</font></a></p></body></html>
EOF;
			} else {
				\header('HTTP/1.0 302 Found');
				\header("Location: {$this->forwardTo}");
			}
			return;
		}
		$title = $this->app->getTitle();
//TODO
$this->addFileCSS('/static/bootstrap/css/bootstrap.min.css');
$this->addFileCSS('/static/bootswatch/spacelab/bootstrap.min.css');
$this->addFileJS('/static/jquery/jquery.min.js');
$this->addFileJS('/static/bootstrap/js/bootstrap.bundle.min.js');
		if (SystemUtils::isShell()) {
			throw new \RuntimeException('Cannot use a WebRender class in this mode: '.$this->getName());
		}
		if (!($this->app instanceof \pxn\phpPortal\WebApp)) {
			throw new \RuntimeException('App not instance of WebApp');
		}
		// render html head
		{
			$headInsert = $this->renderHeadInsert();
			echo $this->_html_head_($title, $headInsert);
		}
		// render html
		{
			$twig = $this->getTwig();
			\pxn\phpPortal\tags\Tag_PageContents::LoadTag($this->app, $twig);
			$tpl = $twig->load('main.twig');
			$tags = [];
			$menus = $this->app->getMenusArray();
			foreach ($menus as $menuName => $menu) {
				$tags[$menuName] = $menu;
			}
			echo $tpl->render($tags);
		}
		// render html foot
		{
			echo $this->_html_foot_();
		}
		@\ob_flush();
	}
/*
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
