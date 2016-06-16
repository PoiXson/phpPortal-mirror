<?php
/*
 * GrowControl Website
 * @copyright 2004-2016
 * @author lorenzo at poixson.com
 * @link http://growcontrol.com/
 */
namespace pxn\gcWebsite\schemas;


class table_blog_entries implements \pxn\phpUtils\pxdb\dbSchema {



	public function getFields() {
		return [
			'entry_id' => [
				'type' => 'increment',
			],
			'title' => [
				'type' => 'varchar',
				'size' => 255,
			],
			'body' => [
				'type' => 'text',
			],
			'timestamp' => [
				'type' => 'datetime',
			],
			'author_id' => [
				'type' => 'int',
				'size' => 11,
				'default' => 0,
			],
			'comment_count' => [
				'type' => 'int',
				'size' => 11,
				'default' => 0,
			],
		];
	}



}
