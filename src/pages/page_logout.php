<?php declare(strict_types = 1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2021
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 * /
namespace pxn\phpPortal\pages;


class page_logout extends \pxn\phpPortal\Page {



	public function __construct(\pxn\phpPortal\WebApp $app) {
		parent::__construct($app);
		\session_destroy();
		$this->app->getRender()->forwardTo('http://127.0.0.1:9999/');
	}



	public function getPageTitle(): string {
		return 'Logout';
	}



	public function getContents(): string {
		throw new \RuntimeException('logout page doesn\'t have contents');
	}



}
*/
