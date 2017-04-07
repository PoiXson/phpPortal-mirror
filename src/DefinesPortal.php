<?php
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2017
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpPortal;


final class DefinesPortal {
	private final function __construct() {}
	public static function init() {}


	const KEY_CONFIG_GROUP_PORTAL = 'portal config';

	const KEY_CFG_TWIG_CACHE_PATH = 'twig cache path';
	const KEY_CFG_CACHER_PATH     = 'cacher path';

	const KEY_CFG_FAILED_PAGE     = 'failed page';
	const KEY_CFG_SITE_TITLE      = 'site title';
	const KEY_CFG_FAV_ICON        = 'fav icon';


	const DEFAULT_RENDER_TYPE = 'main';
	const TEMPLATE_EXTENSION = '.htm';


}
