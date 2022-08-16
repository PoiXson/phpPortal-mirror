<?php declare(strict_types=1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2022
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal\pages;


abstract class StaticTwig extends \pxn\phpPortal\Page {

	protected string $tplFile;



	public function __construct(\pxn\phpPortal\WebApp $app, string $tplFile) {
		parent::__construct($app);
		$this->tplFile = $tplFile;
	}



	public function render(): void {
		$twig = $this->getTwig();
		$tags = [
			'body' => 'pages/home.twig',
		];
		echo $twig->render('main.twig', $tags);
	}



}
