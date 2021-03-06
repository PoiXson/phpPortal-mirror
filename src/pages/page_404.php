<?php declare(strict_types = 1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2021
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal\pages;

use pxn\phpPortal\Router;


class page_404 extends \pxn\phpPortal\Page {

//TODO
//	protected ?string $page_missing;



	public function init(): void {
		if (!\headers_sent()) {
			\header("HTTP/1.0 404 Not Found");
		}
//TODO: san this
//		$this->page_missing = $missing;
	}



	public function render(): void {
		// load template
		$twig = $this->getTwig();
		$tpl = $twig->load('pages/404.twig');
		// tags
		$tags = $this->getTags();
		$tags['page_title'] = '404 Page Not Found';
		$tags['page_missing'] = $this->route ?? '';
		$tags['menus'] = Router::$menus;
		// render page
		$tpl->display($tags);
	}



}
