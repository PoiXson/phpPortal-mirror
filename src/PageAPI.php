<?php declare(strict_types=1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2024
 * @license AGPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal;


abstract class PageAPI extends Page {



	public function getName(): string {
		return 'api';
	}



	public function isDefaultPage(): bool {
		return $this->app->is_api;
	}



	public function getActiveWeight(): int {
		if ($this->app->is_api)
			return 99;
		return parent::getActiveWeight();
	}



}
