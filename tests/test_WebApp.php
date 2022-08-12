<?php declare(strict_types=1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2022
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal\tests;

use pxn\phpPortal\WebApp;


/**
 * @coversDefaultClass \pxn\phpPortal\WebApp
 */
class test_WebApp extends \PHPUnit\Framework\TestCase {



	/**
	 * @covers ::__construct
	 * @covers ::getName
	 * @covers ::getNamespace
	 * @covers ::getVersion
	 * @covers ::check_run_mode
	 */
	public function test_WebApp() {
		$app = new TestApp();
		$this->assertNotNull($app);
		$this->assertEquals(expected: "TestApp",               actual: $app->getName());
		$this->assertEquals(expected: "pxn\\phpPortal\\tests", actual: $app->getNamespace());
		$this->assertEquals(expected: 'z.y.x',                 actual: $app->getVersion());
		$this->assertTrue($app->has_checked_run_state);
	}



}
