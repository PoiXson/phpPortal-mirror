<?php declare(strict_types=1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2022
 * @license AGPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal;

use \pxn\phpUtils\Debug;
use \pxn\phpUtils\xPaths;

use \Twig\Environment;
use \Twig\Loader\FilesystemLoader;


abstract class Page {

	protected ?\pxn\phpPortal\WebApp $app = null;

	protected ?FilesystemLoader $loader = null;
	protected ?Environment      $twig   = null;

	public const DEFAULT_PAGE_WEIGHT = 90;



	public function __construct(\pxn\phpPortal\WebApp $app) {
		$this->app = $app;
	}



	public abstract function getName(): string;



	public function isDefaultPage(): bool {
		return false;
	}
	public function is404Page(): bool {
		return false;
	}
	public function isActivePage(): int {
		return 0;
	}



	public abstract function render(): void;



	public function getTags(): array {
		foreach ($this->menus as $grp_name => $group) {
			if (!\is_array($group)) continue;
			foreach ($group as $name => $menu) {
				if (!\is_array($menu)) continue;
				if (!isset($menu['active']))
					$this->menus[$grp_name][$name]['active'] = false;
			}
		}
		return [
			'debug' => Debug::debug(),
			'menus' => $this->app->menus,
		];
	}



	public function getTwigOptions(): array {
		$debug = Debug::debug();
		$cache = ($debug ? false : xPaths::get('twig-cache'));
		if (empty($cache))
			$cache = false;
		return [
			'cache' => $cache,
			'debug' => $debug,
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
