<?php declare(strict_types=1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2024
 * @license AGPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 * /
namespace pxn\phpPortal;

use \pxn\phpUtils\GeneralUtils;
use \pxn\phpUtils\SystemUtils;
use \pxn\phpUtils\Strings;
use \pxn\phpUtils\San;


trait UserManager {

	protected $user = null;



	protected function initUserManager(): void {
		$sessionName = $this->getSessionName();
		if (!empty($sessionName)) {
			\session_name($sessionName);
		}
		\session_start();
//TODO: logging
//echo \session_id();
		if (isset($_SESSION['username'])) {
			$username = $_SESSION['username'];
			if (!empty($username)) {
				$this->user = $username;
			}
		}
	}



	public function isLoggedIn(): bool {
		return ($this->user !== NULL);
	}



	public function getSessionName(): ?string {
		return NULL;
	}



}
*/
