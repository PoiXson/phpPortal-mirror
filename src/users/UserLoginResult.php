<?php declare(strict_types=1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2024
 * @license AGPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal\users;


enum UserLoginResult {
	case INVALID_DB;
	case INVALID_CAPTCHA;
	case INVALID_LOGIN;
	case ACCOUNT_DISABLED;

	public static function FromString(string|UserLoginResult $result): ?UserLoginResult {
		if (\is_string($result)) {
			$drv = \mb_strtolower(\trim($result));
			return match ($drv) {
				'invalid-db'       => UserLoginResult::INVALID_DB,
				'invalid-captcha'  => UserLoginResult::INVALID_CAPTCHA,
				'invalid-login'    => UserLoginResult::INVALID_LOGIN,
				'account-disabled' => UserLoginResult::ACCOUNT_DISABLED,
				default => null
			};
		} else {
			return $result;
		}
		return null;
	}

	public function toString(): string {
		return match ($this) {
			UserLoginResult::INVALID_DB       => 'invalid-db',
			UserLoginResult::INVALID_CAPTCHA  => 'invalid-captcha',
			UserLoginResult::INVALID_LOGIN    => 'invalid-login',
			UserLoginResult::ACCOUNT_DISABLED => 'account-disabled',
			default => null
		};
	}

}
