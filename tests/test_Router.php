<?php declare(strict_types = 1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2021
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal\tests;

use pxn\phpPortal\router\Router;


/**
 * @coversDefaultClass \pxn\phpPortal\Router
 */
class test_Router extends \PHPUnit\Framework\TestCase {

	const PAGE_404    = 'pxn\\phpPortal\\tests\\pages\\page_404';
	const PAGE_ABOUT  = 'pxn\\phpPortal\\tests\\pages\\page_about';
	const PAGE_HAMMER = 'pxn\\phpPortal\\tests\\pages\\page_hammer';
	const PAGE_CARS   = 'pxn\\phpPortal\\tests\\pages\\page_cars';
	const PAGE_TRUCKS = 'pxn\\phpPortal\\tests\\pages\\page_trucks';

	const PATH_404    = '404';
	const PATH_ABOUT  = 'about';
	const PATH_HAMMER = 'tools/hammer';
	const PATH_CARS   = 'transport/civil/cars';
	const PATH_TRUCKS = 'transport/commercial/trucks';

	protected ?TestApp $app = null;



	protected function initApp(): void {
		if ($this->app != null)
			return;
		$this->app = new TestApp();
		$router = $this->app->getRouter();
		$router->defPage(self::PATH_ABOUT);
		// 404
		$router->addPage(self::PATH_404)
			->setPageClass(self::PAGE_404);
		// test pages
		$router->addPage(self::PATH_ABOUT)
			->setPageClass(self::PAGE_ABOUT);
		$router->addPage(self::PATH_HAMMER)
			->setPageClass(self::PAGE_HAMMER);
		$router->addPage(self::PATH_CARS)
			->setPageClass(self::PAGE_CARS);
		$router->addPage(self::PATH_TRUCKS)
			->setPageClass(self::PAGE_TRUCKS);
	}



	/**
	 * @covers \pxn\phpPortal\WebApp::getRouter
	 * @covers ::__construct
	 * @covers ::addRouter
	 * @covers ::addPage
	 */
	public function test_routes() {
		$this->initApp();
		$router = $this->app->getRouter();
		$routes = $router->getRoutesArray();
		$this->assertEquals(
			expected: [
				404,
				self::PATH_ABOUT,
				'tools',
				'transport',
			],
			actual: \array_keys($routes)
		);
	}
//	protected static function GetRouterRoutes(Router $router): array {
//		$reflect = new \ReflectionClass($router);
//		$prop = $reflect->getProperty('routes');
//		$prop->setAccessible(true);
//		return $prop->getValue($router);
//	}



	/**
	 * @covers \pxn\phpPortal\WebApp::getRouter
	 * @covers ::__construct
	 * @covers ::addRouter
	 * @covers ::addPage
	 * @covers ::getPage
	 */
	public function test_GetDefaultPage() {
		$this->initApp();
		$router = $this->app->getRouter();
		$this->assertNotNull($router);
		// get default page
		$page = $router->getPage();
		$this->assertNotNull($page);
		$this->expectOutputString('About Page');
		$page->render();
	}



}
