<?php declare(strict_types=1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2025
 * @license AGPLv3+ADD-PXN-V1
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal\users;

use \pxn\phpUtils\Debug;

use \pxn\phpPortal\WebApp;

use \pxn\pxdb\dbPool;
use \pxn\pxdb\dbConn;


class UserManager {

	public WebApp $app;
	public dbPool $pool;

	protected ?User $user = null;

	protected bool $use_captcha = true;
	protected bool $use_2fa     = true;



	public function __construct(WebApp $app, dbPool|string $database) {
		$app->initSession();
		$this->app  = $app;
		$this->pool = dbPool::GetPool($database);
	}
	public function init(): void {
		$username = \GetVar(name: 'user', type: 's', src: 's');
		if (!empty($username))
			$this->user = new User(app: $this->app, username: $username);
	}



	public function doLogin(): UserLoginResult {
		if ($this->user !== null) return UserLoginResult::LOGIN_OK;
		$db = $this->pool->get();
		if ($db == null) throw new \RuntimeException('Database not loaded');
		try {
			$capt = \GetVar(name: 'capt', type: 's', src: (Debug::debug()?'gp':'p'));
			$user = \GetVar(name: 'user', type: 's', src: (Debug::debug()?'gp':'p'));
			$pass = \GetVar(name: 'pass', type: 's', src: (Debug::debug()?'gp':'p'));
			unset($_GET ['pass']);
			unset($_POST['pass']);
			$use_captcha = $this->useCaptcha();
			if ($use_captcha
			&&  empty($capt)) return UserLoginResult::GUEST;
			if (empty($user)) return UserLoginResult::GUEST;
			if (empty($pass)) return UserLoginResult::GUEST;
			if ($use_captcha) {
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
			return UserLoginResult::LOGIN_OK;
		} finally {
			$db->release();
		}
	}

	public function doLogout(): void {
		$_SESSION['user' ] = null; unset($_SESSION['user' ]);
		$_SESSION['twofa'] = null; unset($_SESSION['twofa']);
	}

	public function do2FA(): UserLoginResult {
		if ($this->user == null) throw new \RuntimeException('Cannot process 2fa, user object not created');
		if ($this->user->isLoggedIn()) return UserLoginResult::LOGIN_OK;
		$code = \GetVar(name: 'code', type: 'i', src: (Debug::debug()?'gp':'p'));
		unset($_GET ['code']);
		unset($_POST['code']);
		if ($code > 0) {
			if ($this->user->validate2fa($code)) {
				$_SESSION['twofa'] = 1;
				return UserLoginResult::LOGIN_OK;
			} else {
				unset($_SESSION['twofa']);
				return UserLoginResult::INVALID_2FA;
			}
		}
		return UserLoginResult::GUEST;
	}



	public function isLoggedIn(): bool {
		return ($this->user===null ? false : $this->user->isLoggedIn());
	}

	public function getUsername(): ?string {
		return ($this->user===null ? null : $this->user->getUsername());
	}
	public function getEmail(): ?string {
		return ($this->user===null ? null : $this->user->getEmail());
	}
	public function getSecret(): ?string {
		return ($this->user===null ? null : $this->user->getSecret());
	}



	public function useCaptcha(): bool {
		return $this->use_captcha;
	}
	public function use2FA(): bool {
		return $this->use_2fa;
	}



}
