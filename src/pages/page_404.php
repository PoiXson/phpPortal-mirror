<?php
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2019
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 * /
namespace pxn\phpPortal\pages;

use pxn\phpPortal\Website;
use pxn\phpPortal\ConfigPortal;


class page_404 {



	public function getPageTitle() {
		return '404 - Page Not Found!';
	}
	public function getPageContents() {
		$FailedPage = Website::get()
			->getArg( ConfigPortal::getFailedPage() );
		return
"<center>
	<h1>404 - Page Not Found!</h1>
	<h3>Page: {$FailedPage}</h3>
</center>
";
	}



}
*/
