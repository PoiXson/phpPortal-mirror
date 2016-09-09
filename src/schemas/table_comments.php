<?php
/*
 * GrowControl Website
 * @copyright 2004-2016
 * @author lorenzo at poixson.com
 * @link http://growcontrol.com/
 */
namespace pxn\phpPortal\schemas;


class table_comments extends \pxn\phpUtils\pxdb\dbSchema {



	public function initFields() {
		return [
			'comment_id' => [
				'type' => 'increment',
			],
			'context' => [
				'type' => 'varchar',
				'size' => 16,
			],
			'context_id' => [
				'type' => 'int',
				'size' => 11,
				'default' => 0,
			],
			'body' => [
				'type' => 'text',
			],
			'author' => [
				'type' => 'varchar',
				'size' => 32,
			],
			'timestamp' => [
				'type' => 'datetime',
			],
		];
	}



}
