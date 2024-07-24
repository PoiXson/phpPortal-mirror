<?php declare(strict_types=1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2024
 * @license AGPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal\pages;


class page_404 extends \pxn\phpPortal\Page {



	public function getPageName(): string {
		return '404';
	}
	public function getPageTitle(): string {
		return '404';
	}



	public function is404Page(): bool {
		return true;
	}



	public function render(): string {
		if (!\headers_sent())
			\header("HTTP/1.0 404 Not Found");
		$output = "<p>404 - page not found!</p>\n";
		if (!empty($this->app->args)) {
			if (count($this->app->args) == 1) {
				$output .= \reset($this->app->args)."\n";
			} else {
				$output .= "<pre>\n".print_r($this->app->args, true)."</pre>\n";
			}
		}
		return $output;
	}



}
