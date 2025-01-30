<?php declare(strict_types=1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2025
 * @license AGPLv3+ADD-PXN-V1
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal\pages;

use \pxn\phpUtils\utils\WebUtils;


class page_logout extends \pxn\phpPortal\Page {



	public function getPageName(): string {
		return 'logout';
	}
	public function getPageTitle(): string {
		return 'Logout';
	}



	public function render(): string {
		$this->app->usermanager->doLogout();
		$page_return = \GetVar('page_return', 's');
		if (empty($page_return)) $page_return = '/';
		WebUtils::ForwardTo($page_return);
	}



}
