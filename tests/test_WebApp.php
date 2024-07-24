<?php declare(strict_types=1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2024
 * @license AGPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal\tests;

use \pxn\phpUtils\utils\SystemUtils;
use \pxn\phpPortal\tests\site\Website;


/**
 * @coversDefaultClass \pxn\phpPortal\WebApp
 */
class test_WebApp extends \PHPUnit\Framework\TestCase {



	/**
	 * @covers ::__construct
	 * @covers ::getAppName
	 * @covers ::getNamespace
	 * @covers ::getVersion
	 */
	public function test_WebApp() {
		// fake IsWeb()
		{
			$reflect = new \ReflectionClass(SystemUtils::class);
			$prop = $reflect->getproperty('isCLI');
			$prop->setAccessible(true);
			$prop->setValue(false);
			$this->assertTrue(SystemUtils::IsWeb());
		}
		$website = new Website();
		$this->assertNotNull($website);
		$this->assertEquals(expected: 'Website',                     actual: $website->getAppName());
		$this->assertEquals(expected: "pxn\\phpPortal\\tests\\site", actual: $website->getNamespace());
		$this->assertEquals(expected: 'z.y.x',                       actual: $website->getVersion());
		\ob_start();
		// /
		$website->run();
		$this->assertEquals(expected: 'Home Page', actual: \ob_get_contents());
		\ob_clean();
		// /home
		$website->updateURI('/home');
		$website->run();
		$this->assertEquals(expected: 'Home Page', actual: \ob_get_contents());
		\ob_clean();
		// /about
		$website->updateURI('/about');
		$website->run();
		$this->assertEquals(expected: 'About Page', actual: \ob_get_contents());
		\ob_end_clean();
	}



}
