<?php
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2019
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 * /
namespace pxn\phpPortal\pages\wiki;

use Michelf\Markdown;
use Michelf\MarkdownExtra;


// https://michelf.ca/projects/php-markdown/extra/
abstract class Wiki extends \pxn\phpPortal\Page {

	protected $allowExtra = FALSE;



	protected abstract function getPageData();



	public function getPageContents() {
		$data = $this->getPageData();
		if (empty($data)) {
			return 'This page is currently blank.';
		}
		$markdown = $this->getMarkdown();
		$html = $markdown->transform($data);
		return $html;
	}



	public function getMarkdown() {
		$markdown = (
			$this->allowExtra
			? new MarkdownExtra()
			: new Markdown()
		);
		$markdown->enhanced_ordered_list = TRUE;
		return $markdown;
	}
	protected function setAllowExtra($allow=TRUE) {
		$this->allowExtra = $allow;
	}



}
*/
