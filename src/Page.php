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

	protected ?FilesystemLoader $loader = null;
	protected ?\Twig\Environment $twig  = null;



	public function __construct(\pxn\phpPortal\WebApp $app) {
		$tpl_path = $app->getTplPath();
		$this->loader = new FilesystemLoader($tpl_path);
		if ($this->loader == null)
			throw new \RuntimeException('Template path not found: '.$tpl_path);
		$this->twig = new Environment($this->loader);
		if ($this->twig == null)
			throw new \RuntimeException('Failed to make twig instance');
	}



	public abstract function render(): void;

	public abstract function getPageName(): string;



}
