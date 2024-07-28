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



	public abstract function getUsersDB(): dbPool;



	public function render(): string {
		$args = $this->app->getArgs();
		if (isset($args[1]) && isset($args[2])) {
			if ($args[1] === 'img') {
				switch ($args[2]) {
					case 'captcha': return $this->render_captcha(); // /login/img/captcha
					case '2fa':     return $this->render_2fa();     // /login/img/2fa
					default: break;
				}
			}
		}
		return $this->render_login();
	}

	protected function render_login(): string {
		$tags = $this->getTags();
		$tags['page_return'] = \GetVar('page_return', 's');
		$result = $this->app->doLogin();
		if ($result === true) {
			WebUtils::ForwardTo($tags['page_return']);
			exit(0);
		} else
		if ($result !== false) {
			switch ($result) {
				case false: break;
				case UserLoginResult::INVALID_DB:      $tags['login_error'] = 'invalid_db';      break;
				case UserLoginResult::INVALID_CAPTCHA: $tags['login_error'] = 'invalid_captcha'; break;
				case UserLoginResult::INVALID_LOGIN:   $tags['login_error'] = 'invalid_login';   break;
				default: throw new \RuntimeException('Invalid login result: '.$result->toString());
			}
		}
		$tags['enable_captcha'] = $this->app->getEnableCaptcha();
		$twig = $this->getTwig();
		return $twig->render('pages/login.twig', $tags);
	}

	protected function render_captcha(): string {
		WebUtils::NoPageCache();
		$captcha = new Captcha();
		$_SESSION['captcha'] = $captcha->getPhrase();
		$img = $captcha->build();
		\header('Content-type: image/jpeg');
		\imagejpeg($img, null, 90);
		\imagedestroy($img);
		return '';
	}

	protected function render_2fa(): string {
//TODO
	}



}
