<?php declare(strict_types = 1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2021
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal;

use pxn\phpUtils\Strings;


abstract class Page implements PanelContents {

	protected $app = NULL;

	protected $pageName  = NULL;
	protected $pageTitle = NULL;



	public function __construct(\pxn\phpUtils\app\App $app) {
		$this->app = $app;
	}



	public static function ToPageName($page) {
		if ($page instanceof Page)
			return $page->getPageName();
		if (\is_string($page))
			return (string) $page;
		return NULL;
	}



	public function getPageName(): string {
		if ($this->pageName === NULL) {
			$name = \get_called_class();
			$pos = \mb_strrpos($name, '\\');
			if ($pos !== FALSE) {
				$name = \mb_substr($name, $pos + 1);
			}
			if (Strings::StartsWith($name, 'page_')) {
				$name = \mb_substr($name, 5);
			}
			$this->pageName = $name;
		}
		return $this->pageName;
	}



	public function getPageTitle(): ?string {
		return $this->pageTitle;
	}
	public function setPageTitle(?string $title): void {
		$this->pageTitle = (
			empty($title)
			? NULL
			: $title
		);
	}



	public function getTwig(): \Twig\Environment {
		$loader = new \Twig\Loader\FilesystemLoader($twigPath);
		$twigOptions = [
//TODO
			'cache' => FALSE
		];
		$twig = new \Twig\Environment($loader, $twigOptions);
		return $twig;
	}
	public function getTpl(string $filename, ?\Twig\Environment $twig=NULL): \Twig\TemplateWrapper {
		if ($twig == NULL) {
			$twig = $this->getTwig();
		}
		return $twig->load($filename);
	}



}
