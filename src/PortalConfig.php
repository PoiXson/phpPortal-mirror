<?php declare(strict_types=1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2024
 * @license AGPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 * /
namespace pxn\phpPortal;

use \pxn\phpUtils\Config;
use \pxn\phpUtils\San;
use \pxn\phpUtils\Defines;


class ConfigPortal {
	private function __construct() {}

	const CONFIG_GROUP    = DefinesPortal::KEY_CONFIG_GROUP_PORTAL;

	const PAGE_NAME       = DefinesPortal::KEY_CFG_PAGE;

	const FAILED_PAGE     = DefinesPortal::KEY_CFG_FAILED_PAGE;
	const SITE_TITLE      = DefinesPortal::KEY_CFG_SITE_TITLE;
	const FAV_ICON        = DefinesPortal::KEY_CFG_FAV_ICON;

	protected static $cfg = NULL;



	public static function init() {
		if (self::$cfg != NULL) {
			return FALSE;
		}
		self::$cfg = Config::get(DefinesPortal::KEY_CONFIG_GROUP_PORTAL);

		// page name
		self::$cfg->setValidHandler(self::PAGE_NAME, 'string');
		self::$cfg->setDefault(self::PAGE_NAME, 'home');

		// failed page
		self::$cfg->setValidHandler(self::FAILED_PAGE, 'string');

		// site title
		self::$cfg->setValidHandler(self::SITE_TITLE, 'string');

		// fav icon
		self::$cfg->setValidHandler(self::FAV_ICON, 'string');

		return TRUE;
	}



	// page name
	public static function getPageName() {
		$pageName = self::$cfg->getString(
			self::PAGE_NAME
		);
		return self::sanPageName($pageName);
	}
	public static function setPageName($pageName) {
		$currentValue = self::$cfg->peakValue(self::PAGE_NAME);
		if ($currentValue != NULL) {
			fail("Unable to set page to: $pageName  Already set to: $currentValue",
				Defines::EXIT_CODE_USAGE_ERROR);
		}
		self::$cfg->setValue(
			self::PAGE_NAME,
			$pageName
		);
	}
	public static function setDefaultPage($value) {
		self::$cfg->setDefault(
			self::PAGE_NAME,
			$value
		);
	}
	public static function setPageRef(&$value) {
		self::$cfg->setRef(
			self::PAGE_NAME,
			$value
		);
	}
	public static function sanPageName($pageName) {
		return San::AlphaNum(
			$pageName
		);
	}



	// failed page
	public static function getFailedPage() {
		return self::$cfg->getString(
			self::FAILED_PAGE
		);
	}
	public static function setFailedPage($page) {
		self::$cfg->setValue(
			self::FAILED_PAGE,
			$page
		);
	}



	// site title
	public static function getSiteTitle() {
		return self::$cfg->getString(
			self::SITE_TITLE
		);
	}
	public static function setSiteTitle($title) {
		self::$cfg->setValue(
			self::SITE_TITLE,
			$title
		);
	}



	// fav icon
	public static function getFavIcon() {
		return self::$cfg->getString(
			self::FAV_ICON
		);
	}
	public static function setFavIcon($favicon) {
		self::$cfg->setValue(
			self::FAV_ICON,
			$favicon
		);
	}



}
*/
