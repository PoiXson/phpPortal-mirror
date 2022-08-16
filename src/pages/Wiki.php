<?php declare(strict_types=1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2022
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal\pages;


abstract class Wiki extends \pxn\phpPortal\Page {



	public function __construct(\pxn\phpPortal\WebApp $app) {
		parent::__construct($app);
	}



	public function render(): void {
		$twig = $this->getTwig();
		$this->addTwigExt_Markdown();
		$tags = [
			'body' => 'wiki.twig',
		];
		echo $twig->render('main.twig', $tags);
	}



}
