<?php declare(strict_types = 1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2021
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal;

use pxn\phpUtils\Paths;
use pxn\phpUtils\exceptions\NullPointerException;
use pxn\phpUtils\exceptions\RequiredArgumentException;

use Twig\Environment as TwigEnv;
use Twig\Loader\LoaderInterface as TwigLoader;
use Twig\Loader\FilesystemLoader as TwigFileLoader;
use Twig\TemplateWrapper as TwigTmpWrapper;


abstract class Page {

	protected WebApp $app;

	protected string $name;
	protected string $args;

	protected ?TwigEnv $twig = null;



	public function __construct(WebApp $app, string $args, string $name=null) {
		if ($app == null) throw new RequiredArgumentException('app');
		$this->app  = $app;
		$this->args = $args;
		// page name
		if (empty($name)) {
			// page name from class
			$clss = \get_called_class();
			$pos = \mb_strrpos(haystack: $clss, needle: '\\');
			if ($pos === false) {
				$name = $clss;
			} else {
				$name = \mb_substr($clss, $pos+1);
			}
		}
		if (empty($name)) throw new NullPointerException('name');
		if (\str_starts_with(haystack: $name, needle: 'page_'))
			$name = \mb_substr($name, 5);
		$this->name = $name;
		// init page
		$this->init();
	}



	protected function init(): void {
	}



	public function render(): void {
		if (!\headers_sent()) {
			header(header: 'X-Content-Security-Policy: script-src \'self\'', replace: false);
		}
	}



	#########
	## get ##
	#########



	public function getApp(): WebApp {
		return $this->app;
	}



	public function getPageName(): string {
		return $this->name;
	}

	public static function san_page_name(?string $name): ?string {
		if (empty($name)) return null;
		return SanUtils::alpha_num_simple(value: $name);
	}

//TODO
//	public function getPageTitle(): string {
//		return \mb_ucfirst( $this->getName() );
//	}



	##########
	## Twig ##
	##########



	public function getTags(): array {
		return [
			'debug' => \debug(),
			'menus' => [],
		];
	}



	public function getTwig(): TwigEnv {
		if ($this->twig == null) {
			$this->twig =
				new TwigEnv(
					loader:  $this->getTwigLoader(),
					options: $this->getTwigOptions()
				);
		}
		return $this->twig;
	}

	protected function getTwigLoader(): TwigLoader {
		return new TwigFileLoader(paths: $this->getTwigPaths());
	}

	protected function getTwigPaths(): array {
		return [
			Paths::get('html')
		];
	}

	protected function getTwigOptions(): array {
		$debug = \debug();
		return [
			'debug' => $debug,
			'strict_variables' => $debug,
			'cache' => ($debug ? false : Paths::get('twig_cache')),
		];
	}



	protected function prependTwigPath(string $path): void {
		$twig = $this->getTwig();
		$loader = $twig->getLoader();
		$loader->prependPath($path);
	}
	protected function addTwigPath(string $path): void {
		$twig = $this->getTwig();
		$loader = $twig->getLoader();
		$loader->addPath($path);
	}



	protected function addTwigExt_Markdown(): void {
		if (!\class_exists('\\Twig\\Extra\\Markdown\\MarkdownExtension'))
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
								new \Twig\Extra\Markdown\DefaultMarkdown()
							);
				}
			}
		);
	}



}
