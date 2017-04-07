<?php
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2017
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpPortal;

use pxn\phpUtils\Config;
use pxn\phpUtils\Defines;


class ConfigPortal {
	private function __construct() {}

	const CONFIG_GROUP = DefinesPortal::KEY_CONFIG_GROUP_PORTAL;

	const TWIG_CACHE_PATH = DefinesPortal::KEY_CFG_TWIG_CACHE_PATH;
	const CACHER_PATH     = DefinesPortal::KEY_CFG_CACHER_PATH;

	const RENDER_TYPE = DefinesPortal::KEY_CFG_RENDER_TYPE;
	const FAILED_PAGE = DefinesPortal::KEY_CFG_FAILED_PAGE;
	const SITE_TITLE  = DefinesPortal::KEY_CFG_SITE_TITLE;
	const FAV_ICON    = DefinesPortal::KEY_CFG_FAV_ICON;

	protected static $cfg = NULL;



	public static function init() {
		if (self::$cfg != NULL) {
			return FALSE;
		}
		self::$cfg = Config::get(DefinesPortal::KEY_CONFIG_GROUP_PORTAL);

		// twig cache path
		self::$cfg->setValidHandler(self::TWIG_CACHE_PATH, 'string');

		// cacher path
		self::$cfg->setValidHandler(self::CACHER_PATH, 'string');

		// render type
		self::$cfg->setValidHandler(self::RENDER_TYPE, 'string');

		// failed page
		self::$cfg->setValidHandler(self::FAILED_PAGE, 'string');

		// site title
		self::$cfg->setValidHandler(self::SITE_TITLE, 'string');

		// fav icon
		self::$cfg->setValidHandler(self::FAV_ICON, 'string');

		return TRUE;
	}



	// twig cache path
	public static function getTwigCachePath() {
		return self::$cfg->getString(
			self::TWIG_CACHE_PATH
		);
	}
	public static function setTwigCachePath($path) {
		self::$cfg->setValue(
			self::TWIG_CACHE_PATH,
			$path
		);
	}



	// cacher path
	public static function getCacherPath() {
		return self::$cfg->getString(
			self::CACHER_PATH
		);
	}
	public static function setCacherPath($path) {
		self::$cfg->setValue(
			self::CACHER_PATH,
			$path
		);
	}



	// render type
	public static function getRenderType() {
		return self::$cfg->getString(
			self::RENDER_TYPE
		);
	}
	public static function setRenderType($type) {
		self::$cfg->setValue(
			self::RENDER_TYPE,
			$type
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
