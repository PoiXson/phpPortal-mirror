<?php
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpPortal;

use pxn\phpUtils\Numbers;


class Paginate {



	public static function doPaginate($pageNum, $pageLast, $pageWidth=2) {
		$pageLast  = Numbers::MinMax( (int) $pageLast,  1, 1000);
		$pageNum   = Numbers::MinMax( (int) $pageNum,   1, $pageLast);
		$pageWidth = Numbers::MinMax( (int) $pageWidth, 1, 5);
		$pageFrom  = Numbers::MinMax($pageNum - $pageWidth, 2);
		$pageTo    = Numbers::MinMax($pageNum + $pageWidth, FALSE, $pageLast - 1);
		return [
			'current' => $pageNum,
			'last'    => $pageLast,
			'from'    => $pageFrom,
			'to'      => $pageTo,
		];
	}



}
