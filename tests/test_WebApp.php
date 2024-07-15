<?php declare(strict_types=1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2024
 * @license AGPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal\tests;

use \pxn\phpPortal\tests\site\Website;


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
		$website = new Website();
		$this->assertNotNull($website);
		$this->assertEquals(expected: 'Website',                     actual: $website->getName());
		$this->assertEquals(expected: "pxn\\phpPortal\\tests\\site", actual: $website->getNamespace());
		$this->assertEquals(expected: 'z.y.x',                       actual: $website->getVersion());
		$this->assertTrue($website->has_checked_run_state);
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
