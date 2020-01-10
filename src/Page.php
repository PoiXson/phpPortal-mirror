<?php
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2020
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal;


abstract class Page implements PanelContents {

	protected $app = NULL;

	protected $pageTitle = NULL;



	public function __construct(\pxn\phpUtils\app\App $app) {
		$this->app = $app;
	}



	public function getPageTitle(): string {
		return $this->pageTitle;
	}
	public function setPageTitle($title): void {
		$this->pageTitle = (
			empty($title)
			? NULL
			: $title
		);
	}
	public function getTitle(): string {
		if (!empty($this->pageTitle))
			return $this->pageTitle;
//TODO
		return 'TITLE';
	}



	public function getTwig(): \Twig\Environment {
		$loader = new \Twig\Loader\FilesystemLoader($twigPath);
		$twigOptions = [
//TODO
			'cache' => false
		];
		$twig = new \Twig\Environment($loader, $twigOptions);
		return $twig;
	}
	public function getTpl(string $filename, ?\Twig\Environment $twig=null): \Twig\TemplateWrapper {
		if ($twig == null) {
			$twig = $this->getTwig();
		}
		return $twig->load($filename);
	}



}
