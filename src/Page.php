<?php declare(strict_types=1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2022
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal;

use \Twig\Environment;
use \Twig\Loader\FilesystemLoader;

use \pxn\phpUtils\Debug;
use \pxn\phpUtils\xPaths;


abstract class Page {

	protected ?\pxn\phpPortal\WebApp $app = null;

	protected ?FilesystemLoader $loader = null;
	protected ?Environment $twig  = null;



	public function __construct(\pxn\phpPortal\WebApp $app) {
		$this->app = $app;
	}



	public function isValidPage(): bool {
		return true;
	}
	public function is404Page(): bool {
		return false;
	}



	public abstract function render(): void;



	public function getTwigOptions(): array {
//TODO
		return [
			'cache' => false,
			'debug' => true,
			'strict_variables' => true,
		];
	}
	public function getTwigLoader(): FilesystemLoader {
		if ($this->loader == null) {
			$tpl_paths = xPaths::get('html');
			if (empty($tpl_paths))
				throw new \RuntimeException('Template paths not found');
			$this->loader = new FilesystemLoader($tpl_paths);
			if ($this->loader == null)
				throw new \RuntimeException('Failed to get template loader');
		}
		return $this->loader;
	}
	public function getTwig(): Environment {
		if ($this->twig == null) {
			$loader = $this->getTwigLoader();
			$options = $this->getTwigOptions();
			$this->twig = new Environment($this->loader, $options);
		}
		if ($this->twig == null)
			throw new \RuntimeException('Failed to make twig instance');
		return $this->twig;
	}



}
