<?php declare(strict_types=1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2022
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal;


abstract class WebApp extends \pxn\phpUtils\app\xApp {

	protected ?\Composer\Autoload\ClassLoader $loader = null;

	protected ?Router $router = null;



    public function __construct(?\Composer\Autoload\ClassLoader $loader=NULL) {
		parent::__construct();
		$this->loader = $loader;
	}



	public function run(): void {
echo "render page\n";
	}



}
