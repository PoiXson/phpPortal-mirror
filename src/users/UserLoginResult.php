<?php declare(strict_types=1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2025
 * @license AGPLv3+ADD-PXN-V1
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal\users;


enum UserLoginResult {
	case GUEST;
	case LOGIN_OK;
	case INVALID_DB;
	case INVALID_CAPTCHA;
	case INVALID_LOGIN;
	case INVALID_2FA;
	case ACCOUNT_DISABLED;

	public static function FromString(string|UserLoginResult $result): ?UserLoginResult {
		if (\is_string($result)) {
			$drv = \mb_strtolower(\trim($result));
			return match ($drv) {
				'guest'            => UserLoginResult::GUEST,
				'login-ok'         => UserLoginResult::LOGIN_OK,
				'invalid-db'       => UserLoginResult::INVALID_DB,
				'invalid-captcha'  => UserLoginResult::INVALID_CAPTCHA,
				'invalid-login'    => UserLoginResult::INVALID_LOGIN,
				'invalid-2fa'      => UserLoginResult::INVALID_2FA,
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
			UserLoginResult::GUEST            => 'guest',
			UserLoginResult::LOGIN_OK         => 'login-ok',
			UserLoginResult::INVALID_DB       => 'invalid-db',
			UserLoginResult::INVALID_CAPTCHA  => 'invalid-captcha',
			UserLoginResult::INVALID_LOGIN    => 'invalid-login',
			UserLoginResult::INVALID_2FA      => 'invalid-2fa',
			UserLoginResult::ACCOUNT_DISABLED => 'account-disabled',
			default => null
		};
	}

}
