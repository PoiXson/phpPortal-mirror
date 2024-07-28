<?php declare(strict_types=1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2024
 * @license AGPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal\users;

use \pxn\phpPortal\WebApp;

use \PragmaRX\Google2FAQRCode\Google2FA;


class User {

	protected WebApp $app;

	protected string $username;
	protected string $email;
	protected string $secret;

	// null=disabled true=ok false=not-ready
	protected ?bool $twofa = null;



	public function __construct(WebApp $app, string $username) {
		$this->app      = $app;
		$this->username = $username;
		if ($app->usermanager->use2FA())
			$this->twofa = (\GetVar(name: 'twofa', type: 'b', src: 's') === true);

		$db = $app->usermanager->pool->get();
		try {
			$db->prepare('SELECT * FROM `__TABLE__users` WHERE `user`=:user');
			$db->setString(':user', $username);
			$db->exec();
			if (!$db->hasNext()) throw new \RuntimeException('Failed to load user');
			$this->email  = $db->getString('email');
			$this->secret = $db->getString('secret');
			if (empty($this->secret)) {
				$this->secret = (new Google2FA())->generateSecretKey(32);
				$db->prepare('UPDATE `__TABLE__users` SET `secret`=:secret WHERE `user`=:user');
				$db->setString(':user',   $username);
				$db->setString(':secret', $this->secret);
				$db->exec();
			}
		} finally {
			$db->release();
		}
	}



	public function validate2fa(int $code): bool {
		if ($this->twofa === null) throw new \RuntimeException('cannot validate disabled 2fa');
		$result = (new Google2FA())->verifyKey((string)$this->secret, (string)$code);
		if ($result === true)
			$this->twofa = true;
		return $this->twofa;
	}



	public function isLoggedIn(): bool {
		return ($this->twofa !== false);
	}

	public function getUsername(): string {
		return $this->username;
	}
	public function getEmail(): string {
		return $this->email;
	}
	public function getSecret(): string {
		return $this->secret;
	}



}
