<?php
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2020
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal\tags;


class MenuBuilderTag extends \Twig\Extension\AbstractExtension {



	public static function loadTag(\Twig\Environment $twig) {
		$twig->addExtension(new MenuBuilderTag());
	}
	public function __construct() {
	}



	public function getFunctions(): array {
		$tagName  = 'MenuBuilder';
		$callback = [ $this, 'buildMenu' ];
		$options  = [ 'is_safe' => ['html'] ];
		return [
			new \Twig\TwigFunction(
				$tagName,
				$callback,
				$options
			)
		];
	}



	public function buildMenu(string $menuName): string {
return "--- MENU $menuName ---";
//		return [
//			'a',
//			'b',
//			'c'
//		];
	}



}
