<?php
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2020
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 * /
namespace pxn\phpPortal\schemas;

use pxn\pxdb\dbField;


class table_users extends \pxn\pxdb\dbTableSchema {



	public function initFields() {
		$this->fields = [
			(new dbField('user_id',  'increment'   )),
			(new dbField('username', 'varchar', 16 ))
				->setUnique(TRUE),
			(new dbField('email',    'varchar', 255))
				->setNullable(TRUE),
			(new dbField('password', 'varchar', 255))
		];
	}



}
*/
