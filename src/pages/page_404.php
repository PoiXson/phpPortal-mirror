<?php declare(strict_types=1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2022
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal\pages;


class page_404 extends \pxn\phpPortal\Page {



	public static function init($url): bool {
		return false;
	}



	public function render(): void {
		if (!\headers_sent()) {
			\header("HTTP/1.0 404 Not Found");
		}
		echo "<p>404 - page not found!</p>\n";
		if (!empty($this->app->args)) {
			if (count($this->app->args) == 1) {
				echo \reset($this->app->args)."\n";
			} else {
				echo "<pre>\n".print_r($this->app->args, true)."</pre>\n";
			}
		}
	}



	public function getPageName(): string {
		return '404';
	}



}
