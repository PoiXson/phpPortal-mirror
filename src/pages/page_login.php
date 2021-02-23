<?php declare(strict_types = 1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2021
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 * /
namespace pxn\phpPortal\pages;

use pxn\phpUtils\GeneralUtils;


class page_login extends \pxn\phpPortal\Page {



	public function __construct(\pxn\phpPortal\WebApp $app) {
		parent::__construct($app);
		$action = GeneralUtils::getVar('action', 's', 'p');
		if (!empty($action)) {
			$username = GeneralUtils::getVar('username', 's', 'p');
			$password = GeneralUtils::getVar('password', 's', 'p');
			$result = \pam_auth($username, $password, $err);
			if ($result === TRUE) {
				$_SESSION['username'] = $username;
				$this->app->getRender()->forwardTo('/');
			} else {
//TODO: logging
				echo "$err<br />\n";
			}
		}
	}



	public function getPageTitle(): string {
		return 'Login';
	}



	public function getContents(): string {
		$tpl = $this->getTpl('pages/login.twig');
		return $tpl->render();
	}



}
*/
