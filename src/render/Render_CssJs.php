<?php
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2020
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal\render;


trait Render_CssJs {

	// css
	protected $cssFiles  = [];
	protected $cssBlocks = [];
	// js
	protected $jsFiles   = [];
	protected $jsBlocks  = [];



	protected function renderHeadInsert(): string {
		$output = '';
		// css files
		if (\count($this->cssFiles) > 0) {
			$output .= "<!-- css files -->\n";
			foreach ($this->cssFiles as $file) {
				$output .= "<link rel=\"stylesheet\" href=\"$file\" />\n";
			}
			$output .= "\n";
		}
		// css blocks
		if (\count($this->cssBlocks) > 0) {
			$output .= "<!-- css blocks -->\n";
			foreach ($this->cssBlocks as $block) {
				$output .= "<style>\n$block\n</style>\n";
			}
			$output .= "\n";
		}
		// js files
		if (\count($this->jsFiles) > 0) {
			$output .= "<!-- js files -->\n";
			foreach ($this->jsFiles as $file) {
				$output .= "<script src=\"$file\"></script>\n";
			}
			$output .= "\n";
		}
		// js blocks
		if (\count($this->jsBlocks) > 0) {
			$output .= "<!-- js blocks -->\n";
			foreach ($this->jsBlocks as $block) {
				$output .= "<script>\n$block\n</script>\n";
			}
			$output .= "\n";
		}
		if (empty($output)) {
			return $output;
		}
		return "\n\n$output";
	}



	// css/js file
	public function addFileCSS(string $file): void {
		if (\in_array($file, $this->cssFiles))
			return;
		$this->cssFiles[] = $file;
	}
	public function addFileJS(string $file): void {
		if (\in_array($file, $this->jsFiles))
			return;
		$this->jsFiles[] = $file;
	}



	// css/js code
	public function addBlockCSS(string $block): void {
		$this->cssBlocks[] = $block;
	}
	public function addBlockJS(string $block): void {
		$this->jsBlocks[] = $block;
	}



}
