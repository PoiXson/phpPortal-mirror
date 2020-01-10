<?php
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2020
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal\pages;


class page_404 extends \pxn\phpPortal\Page {

	protected $app;



	public function __construct(\pxn\phpPortal\WebApp $app) {
		$this->app = $app;
	}



	public function getPageTitle(): string {
		return '404 - Page Not Found!';
	}



	public function getContents(): string {
		$FailedPage = $this->app->getPage();
		return <<<EOF
<center>
	<h1>404 - Page Not Found!</h1>
	<h3>Page: {$FailedPage}</h3>
</center>
EOF;
	}



}
