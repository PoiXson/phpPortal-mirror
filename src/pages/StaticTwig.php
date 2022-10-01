<?php declare(strict_types=1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2022
 * @license AGPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal\pages;

use \pxn\phpUtils\xPaths;
use \pxn\phpUtils\utils\PathUtils;
use \pxn\phpUtils\Debug;


abstract class StaticTwig extends \pxn\phpPortal\Page {

	protected string $page_file = '';
	protected string $tpl_file  = '';



	public function isActivePage(): int {
		$uri = $this->app->uri;
		if (empty($uri)) {
			// default
			if ($this->isDefaultPage())
				return self::DEFAULT_PAGE_WEIGHT;
		} else {
			// page name
			if ($uri == $this->page_file)
				return 95;
		}
		return 0;
	}



	public function getFile(): string {
		if (empty($this->page_file)) {
			$this->page_file = $this->getName();
		}
		if (empty($this->page_file))
			throw new \RuntimeException('Unknown page file');
		if (empty($this->tpl_file)) {
			$file = \implode('/', [
				xPaths::get('html'),
				$this->page_file,
			]);
			if (!\str_ends_with($file, '.twig'))
				$file .= '.twig';
			// check safe path
			$file = PathUtils::NormPath($file);
			if (!\str_starts_with($file, xPaths::get('html')))
				throw new \RuntimeException("Invalid page path: $file");
			$this->tpl_file = $file;
		}
		return $this->tpl_file;
	}



	public function render(): void {
		$file = $this->getFile();
		$f = PathUtils::TrimPath($file, xPaths::get('html'));
		if ($f === false)
			throw new \RuntimeException("Invalid page path: $file");
		$twig = $this->getTwig();
		$tags = $this->getTags();
		echo $twig->render($f, $tags);
	}



}
