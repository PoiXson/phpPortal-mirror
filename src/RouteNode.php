<?php declare(strict_types = 1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2021
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 * /
namespace pxn\phpPortal;


abstract class RouteNode {

	protected WebApp  $app;
	protected ?Router $parent;
	protected string  $key;



	public function __construct(WebApp $app, ?Router $parent, string $key='') {
		$this->app    = $app;
		$this->parent = $parent;
		$this->key    = $key;
	}



	public function getApp(): WebApp {
		return $this->app;
	}
	public function getParent(): ?Router {
		return $this->parent;
	}
	public function getKey(): string {
		return $this->key;
	}



}
*/
