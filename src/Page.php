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


abstract class Page {

	protected ?\pxn\phpPortal\WebApp $app = null;

	protected ?FilesystemLoader $loader = null;
	protected ?Environment $twig  = null;



	public function __construct(\pxn\phpPortal\WebApp $app) {
		$this->app = $app;
	}



	public abstract function render(): void;

	public abstract function getPageName(): string;



	public function getTwigOptions(): array {
		return [
			'cache' => false,
			'debug' => true,
			'strict_variables' => true,
		];
	}
	public function getTwigLoader(): FilesystemLoader {
		if ($this->loader == null) {
			$tpl_paths = $this->app->getTplPaths();
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



	public function addTwigExt_Markdown(): void {
		if (!\class_exists('\\Twig\\Extra\\Markdown\\LeagueMarkdown'))
			throw new \RuntimeException('Markdown extention not available');
		$twig = $this->getTwig();
		$twig->addExtension(
			new \Twig\Extra\Markdown\MarkdownExtension()
		);
		$twig->addRuntimeLoader(
			new class implements \Twig\RuntimeLoader\RuntimeLoaderInterface {
				public function load($class) {
					if (\Twig\Extra\Markdown\MarkdownRuntime::class === $class)
						return
							new \Twig\Extra\Markdown\MarkdownRuntime(
								new \Twig\Extra\Markdown\LeagueMarkdown()
							);
				}
			}
		);
	}



}
