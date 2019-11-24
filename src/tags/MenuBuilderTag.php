<?php
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2019
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal\tags;


class MenuBuilderTag extends \Twig\Extension\AbstractExtension {



	public function getFunctions(): array {
		return [
			new \Twig\TwigFunction(
				'MenuBuilder',
				[ $this, 'buildMenu' ],
				[ 'is_safe' => ['html'] ]
			)
		];
	}



	public function buildMenu(): array {
		return [
			'a',
			'b',
			'c'
		];
	}



}
