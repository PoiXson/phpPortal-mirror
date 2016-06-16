<?php
/*
 * GrowControl Website
 * @copyright 2004-2016
 * @author lorenzo at poixson.com
 * @link http://growcontrol.com/
 */
namespace pxn\phpPortal\schemas;


class table_users implements \pxn\phpUtils\pxdb\dbSchema {



	public function getFields() {
		return [
			'user_id' => [
				'type' => 'increment',
			],
			'username' => [
				'type' => 'varchar',
				'size' => 16,
				'nullable' => FALSE,
				'unique' => TRUE,
			],
			'email' => [
				'type' => 'varchar',
				'size' => 255,
			],
			'password' => [
				'type' => 'varchar',
				'size' => 255,
			],
		];
	}



}
