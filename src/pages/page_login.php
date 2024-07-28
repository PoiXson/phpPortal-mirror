<?php declare(strict_types=1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2024
 * @license AGPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal\pages;

use \pxn\phpUtils\utils\WebUtils;

use \pxn\phpPortal\users\UserLoginResult;
use \pxn\phpPortal\secimg\Captcha;

use \pxn\pxdb\dbPool;

use \PragmaRX\Google2FAQRCode\Google2FA;


abstract class page_login extends \pxn\phpPortal\Page {



	public function getPageName(): string {
		return 'login';
	}
	public function getPageTitle(): string {
		return 'Login';
	}



	public function render(): string {
		$args = $this->app->getArgs();
		if (isset($args[1]) && isset($args[2])) {
			if ($args[1] === 'img') {
				switch ($args[2]) {
					case 'captcha': return $this->render_img_captcha(); // /login/img/captcha
					case '2fa':     return $this->render_img_2fa();     // /login/img/2fa
					default: break;
				}
			}
		}
		$page_return = \GetVar('page_return', 's');
		$result = $this->render_login($page_return);
		if ($result === true)
			$result = $this->render_2fa($page_return);
		if ($result === true) {
			WebUtils::ForwardTo($page_return);
			exit(0);
		}
		return (string) $result;
	}

	protected function render_login(?string $page_return): string|bool {
		$tags = $this->getTags();
		$result = $this->app->usermanager->doLogin();
		switch ($result) {
			case UserLoginResult::LOGIN_OK: return true;
			case UserLoginResult::GUEST:    break;
			case UserLoginResult::INVALID_DB:      $tags['login_error'] = 'invalid-db';      break;
			case UserLoginResult::INVALID_CAPTCHA: $tags['login_error'] = 'invalid-captcha'; break;
			case UserLoginResult::INVALID_LOGIN:   $tags['login_error'] = 'invalid-login';   break;
			default: throw new \RuntimeException('Invalid login result: '.$result->toString());
		}
		$tags['enable_captcha'] = $this->app->usermanager->useCaptcha();
		$tags['page_return']    = $page_return;
		$twig = $this->getTwig();
		return $twig->render('pages/login.twig', $tags);
	}

	protected function render_2fa(?string $page_return): string|bool {
		$tags = $this->getTags();
		$result = $this->app->usermanager->do2FA();
		switch ($result) {
			case UserLoginResult::LOGIN_OK: return true;
			case UserLoginResult::GUEST:    break;
			case UserLoginResult::INVALID_2FA: $tags['login_error'] = 'invalid_2fa'; break;
			default: throw new \RuntimeException('Invalid login result: '.$result->toString());
		}
		$tags['page_return'] = $page_return;
		$twig = $this->getTwig();
		return $twig->render('pages/2fa.twig', $tags);
	}



	protected function render_img_captcha(): string {
		WebUtils::NoPageCache();
		$captcha = new Captcha();
		$_SESSION['captcha'] = $captcha->getPhrase();
		$img = $captcha->build();
		\header('Content-type: image/jpeg');
		\imagejpeg($img, null, 90);
		\imagedestroy($img);
		return '';
	}

	protected function render_img_2fa(): string {
		WebUtils::NoPageCache();
		$user   = $this->app->usermanager->getUsername();
		$email  = $this->app->usermanager->getEmail();
		$secret = $this->app->usermanager->getSecret();
		$svg = (new Google2FA())->getQRCodeInline(
			$user,
			$email,
			$secret
		);
		\header('Content-type: image/svg+xml');
		return $svg;
	}



}
