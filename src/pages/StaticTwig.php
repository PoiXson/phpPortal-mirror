<?php declare(strict_types=1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2022
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal\pages;

use \pxn\phpUtils\xPaths;
use \pxn\phpUtils\utils\PathUtils;


abstract class StaticTwig extends \pxn\phpPortal\Page {

	protected string $page_file;
	protected ?string $tpl_file = null;



	public function __construct(\pxn\phpPortal\WebApp $app, string $page_file) {
		parent::__construct($app);
		$this->page_file = $page_file;
	}



	public function getFile(): string {
		if (empty($this->tpl_file)) {
			$file = \implode('/', [
				xPaths::get('html'),
				$this->page_file,
			]);
			if (!\str_ends_with($file, '.twig'))
				$file .= '.twig';
			// check safe path
			$file = PathUtils::NormPath($file);
			if (!\str_starts_with($file, xPaths::get('html'))
				throw new \RuntimeException("Invalid page path: $file");
			$this->tpl_file = $file;
		}
		return $this->tpl_file;
	}

	public function isValidPage(): bool {
		return \is_file( $this->getFile() );
	}



	public function render(): void {
		$file = $this->getFile();
		$f = PathUtils::TrimPath($file, xPaths::get('html'));
		if ($f === false)
			throw new \RuntimeException("Invalid page path: $file");
		$twig = $this->getTwig();
		$tags = [];
		$content = $twig->render($f, $tags);
		echo $this->render_main($content);
	}



}
