<?php declare(strict_types=1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2024
 * @license AGPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 * /
namespace pxn\phpPortal\pages;


abstract class Blog extends \pxn\phpPortal\Page {

	protected string $blog_name = '';



	public function __construct(\pxn\phpPortal\WebApp $app, string $blog_name='') {
		parent::__construct($app);
		$this->blog_name = $blog_name;
	}



	public function render(): string {
		$converter = Wiki::getMarkdownConverter();
		$twig = $this->getTwig();
		$tags = [
			'debug' => Debug::debug(),
		];
		return $twig->render('blog.twig', $tags);
	}



}
*/
