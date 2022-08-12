<?php declare(strict_types=1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2022
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


abstract class Page {

	protected WebApp $app;

	protected string $key = '';
	protected array $args = [];

	protected string $title = '';

	protected ?TwigEnv $twig = null;



	public function __construct(WebApp $app, string $key=null) {
		if ($app == null) throw new RequiredArgumentException('app');
		$this->app = $app;
		// page name
		if (empty($key)) {
			// page name from class
			$clss = \get_called_class();
			$pos = \mb_strrpos(haystack: $clss, needle: '\\');
			if ($pos === false) {
				$key = $clss;
			} else {
				$key = \mb_substr($clss, $pos+1);
			}
		}
		if (empty($key)) throw new NullPointerException('key');
		if (\str_starts_with(haystack: $key, needle: 'page_'))
			$key = \mb_substr($key, 5);
		$this->key = $key;
		// init page
		$this->init();
	}



	protected function init(): void {}



	public function doRender(): void {
		if (!\headers_sent()) {
			header(header: 'X-Content-Security-Policy: script-src \'self\'', replace: false);
		}
		// render api
		if (Router::isAPI()) {
			$result = $this->render_api();
			if (\is_array($result)) {
				echo \json_encode($result);
			}
			return;
		}
		// render normal
		$this->render();
	}
	public function render(): void {
		throw new \RuntimeException('Unhandled page: '.$this->getPageName());
	}
	public function render_api(): ?array {
		throw new \RuntimeException('Unhandled api: '.$this->getPageName());
	}



	#########
	## get ##
	#########



	public function getApp(): WebApp {
		return $this->app;
	}



	public function getPageName(): string {
		return $this->key;
	}

	public static function san_page_name(?string $key): ?string {
		if (empty($key)) return null;
		return SanUtils::alpha_num_simple(value: $key);
	}



	public function getPageTitle(): string {
		if (!empty($this->title))
			return $this->title;
		return \ucwords( $this->getPageName() );
	}
	public function setPageTitle(string $title): void {
		$this->title = $title;
	}



	public function getArgs(): array {
		return $this->args;
	}
	public function setArgs(array $args): void {
		$this->args = $args;
	}



	##########
	## Twig ##
	##########



	public function getTags(): array {
		return [
			'debug' => \debug(),
			'menus' => [],
			'page_title' => $this->getPageTitle(),
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
		if (\debug()) {
			if (!$this->twig->hasExtension('\\Twig\\Extension\\DebugExtension'))
				$this->twig->addExtension(new \Twig\Extension\DebugExtension());
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
