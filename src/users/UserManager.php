<?php declare(strict_types=1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2024
 * @license AGPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal\users;

use \pxn\pxdb\dbPool;
use \pxn\pxdb\dbConn;
use \pxn\phpUtils\Debug;


trait UserManager {

	protected ?string $user = null;

	protected bool $useCaptcha = true;
	protected bool $use2fa     = true;



	protected function initUserManager(): void {
		\session_name($this->getSessionName());
		\session_start();
//TODO: logging
//\dd(\session_id());
		$user = \GetVar('user', 's', 's');
		if (!empty($user))
			$this->user = $user;
	}



	public function doLogin(): bool|UserLoginResult {
		$pool = $this->getUsersDB();
		$db   = $pool->get();
		if ($db == null) throw new \RuntimeException('Database not loaded');
		try {
			$capt = \GetVar(name: 'capt', type: 's', src: (Debug::debug()?'gp':'p'));
			$user = \GetVar(name: 'user', type: 's', src: (Debug::debug()?'gp':'p'));
			$pass = \GetVar(name: 'pass', type: 's', src: (Debug::debug()?'gp':'p'));
			unset($_GET ['pass']);
			unset($_POST['pass']);
			$useCaptcha = $this->getEnableCaptcha();
			if ($useCaptcha
			&&  empty($capt)) return false;
			if (empty($user)) return false;
			if (empty($pass)) return false;
			if ($useCaptcha) {
				$capt_session = \GetVar('captcha', 's', 's');
				unset($_SESSION['captcha']);
				if ($capt_session !== $capt)
					return UserLoginResult::INVALID_CAPTCHA;
			}
			$db->prepare('SELECT * FROM `__TABLE__users` WHERE `user`=:user');
			$db->setString(':user', $user);
			$db->exec();
			if (!$db->hasNext())
				return UserLoginResult::INVALID_LOGIN;
			$row = $db->getRow();
			if (!isset($row['user']))   throw new \RuntimeException('Username field not found');
			if ($row['user'] !== $user) throw new \RuntimeException('Usernames don\'t match; this shouldn\'t happen');
			if (!isset($row['pass']))   throw new \RuntimeException('Password field not found');
			$hash = $row['pass'];
			if (empty($hash)) return UserLoginResult::ACCOUNT_DISABLED;
			// check hash
			if (\str_starts_with(haystack: $hash, needle: '$')) {
				if (\password_verify($pass, $hash) !== true)
					return UserLoginResult::INVALID_LOGIN;
			// plain text
			} else {
				if ($pass !== $hash) return UserLoginResult::INVALID_LOGIN;
			}
			// update hash
			if (\password_needs_rehash($hash, \PASSWORD_DEFAULT)) {
				$db->prepare('UPDATE `__TABLE__users` SET `pass`=:pass WHERE `user`=:user');
				$db->setString(':user', $user);
				$db->setString(':pass', \password_hash($pass, \PASSWORD_DEFAULT));
				$db->exec();
			}
			unset($pass);
			unset($hash);
			$_SESSION['user'] = $user;
			return true;
		} finally {
			$db->release();
		}
		return false;
	}



	public function getUsersDB(?string $dbName=null): dbPool {
		return dbPool::GetPool($dbName);
	}



	public function isLoggedIn(): bool {
		return ($this->user !== null);
	}



	public function getSessionName(): ?string {
		return null;
	}

	public function getEnableCaptcha(): bool {
		return $this->useCaptcha;
	}
	public function getEnable2FA(): bool {
		return $this->use2fa;
	}



}
