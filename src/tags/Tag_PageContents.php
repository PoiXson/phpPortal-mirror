<?php
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2020
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal\tags;

use pxn\phpPortal\WebApp;


class Tag_PageContents extends \Twig\Extension\AbstractExtension {

	protected $app;



		$twig->addExtension(
			new PageContentsTag($app)
		);
	public static function LoadTag(WebApp $app, \Twig\Environment $twig): Tag_PageContents {
	}
	public function __construct(WebApp $app) {
		$this->app = $app;
	}



	public function getFunctions(): array {
		$tagName  = 'PageContents';
		$callback = [ $this, 'getPageContents' ];
		$options  = [ 'is_safe' => ['html'] ];
		return [
			new \Twig\TwigFunction(
				$tagName,
				$callback,
				$options
			)
		];
	}



	public function getPageContents(): string {
		return $this->app->getPageRendered();
	}



}
