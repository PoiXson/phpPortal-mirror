<?php
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2020
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal\tags;


class PageContentsTag extends \Twig\Extension\AbstractExtension {



	public function getFunctions(): array {
		return [
			new \Twig\TwigFunction(
				'PageContents',
				[ $this, 'getPageContents' ],
				[ 'is_safe' => ['html'] ]
			)
		];
	}



	public function getPageContents(): string {
		return "\n\n\n<br /><p>!!! <b>PAGE CONTENTS</b> !!!</p>\n\n\n";
	}



}
