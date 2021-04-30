<?php declare(strict_types = 1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2021
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal;

use pxn\phpUtils\utils\SystemUtils;
use pxn\phpUtils\Paths;


abstract class WebApp extends \pxn\phpUtils\app\xApp {

	protected ?Router $router = null;



	public function __construct() {
		parent::__construct();
	}

	protected function init(): void {
		parent::init();
		// load configs
		$this->load_configs();
		// pages
		$this->load_pages();
	}

	protected function check_run_mode(): void {
		SystemUtils::AssertWeb();
	}



	protected function load_paths(): void {
		parent::load_paths();
		Paths::set(key: 'twig_cache', path: '{entry}/../twig_cache');
		Paths::set(key: 'html',       path: '{entry}/../html');
	}

	protected function load_configs(): void {
	}

	protected function load_pages(): void {
		$router = $this->getRouter();
		$menus = &$this->getMenus();
		// default pages
		$router->addPage(pattern: '404', clss: 'pxn\\phpPortal\\pages\\page_404');
	}



	public function run(): void {
		// load page
		$router = $this->getRouter();
		$page = $router->getPage();
		$page->render();
	}



	public function getRouter(): Router {
		if ($this->router == null)
			$this->router = new Router($this);
		return $this->router;
	}



}
