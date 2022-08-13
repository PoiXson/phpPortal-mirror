<?php declare(strict_types=1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2022
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal;

use \pxn\phpUtils\utils\StringUtils;


abstract class WebApp extends \pxn\phpUtils\app\xApp {

	protected ?\Composer\Autoload\ClassLoader $loader = null;

	public $page = null;
	public ?string $uri = null;



    public function __construct(?\Composer\Autoload\ClassLoader $loader=NULL) {
		parent::__construct();
		$this->loader = $loader;
	}



	public abstract function loadPages(): void;

	public function run(): void {
		if (empty($this->uri)) {
			$this->uri = (
				isset($_SERVER['REQUEST_URI'])
				? $_SERVER['REQUEST_URI'] : ''
			);
		}
		$this->uri = StringUtils::trim($this->uri, '/');
		// load pages
		$this->loadPages();
		if ($this->page == null) {
			echo "404 Not found!\n";
			exit(1);
		}
		// new instance
		if (\is_string($this->page)) {
			if (\str_contains($this->page, '\\')) {
				$this->page = new $this->page($this);
			} else {
				echo $this->page;
				return;
			}
		}
		// render page
		if (\is_subclass_of($this->page, '\\pxn\\phpPortal\\Page', false)) {
			echo $this->page->render();
			return;
		}
		echo 'Unknown page type: '.\get_class($this->page);
	}



	public function getTplPath(): string {
//TODO
		return $_SERVER['DOCUMENT_ROOT'].'/../html';
	}



}
