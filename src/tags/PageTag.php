<?php
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2019
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal\tags;


class PageTag extends \Twig\Extension\AbstractExtension {



	public function getFunctions() {
		return [
			new \Twig\TwigFunction(
				'PageContents',
				[ $this, 'getPageContents' ],
				[ 'is_safe' => ['html'] ]
			)
		];
	}



	public function getPageContents() {
		return "\n\n\n<br /><p>!!! <b>PAGE CONTENTS</b> !!!</p>\n\n\n";
	}



}
