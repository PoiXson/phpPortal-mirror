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

	protected ?TestApp $app = null;



	protected function initApp(): void {
		if ($this->app != null)
			return;
		$this->app = new TestApp();
		$router = $this->app->getRouter();
		$router->defPage('about');
		// 404
		$router->addPage(pattern: '404', clss: self::PAGE_404);
		// test pages
		$router->add([
			'about'                 => self::PAGE_ABOUT,
			'tools/hammer'          => self::PAGE_HAMMER,
			'things/transport/cars' => self::PAGE_CARS,
		]);
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
		$this->assertEquals(
			expected: [
				'404'                   => self::PAGE_404,
				'about'                 => self::PAGE_ABOUT,
				'tools/hammer'          => self::PAGE_HAMMER,
				'things/transport/cars' => self::PAGE_CARS,
			],
			actual: $router->getRoutes()
//			actual: self::GetRouterRoutes($router)
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
