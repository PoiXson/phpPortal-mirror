<?php
/*
 * GrowControl Website
 * @copyright 2004-2016
 * @author lorenzo at poixson.com
 * @link http://growcontrol.com/
 */
namespace pxn\phpPortal\schemas;

use pxn\pxdb\dbField;


class table_users extends \pxn\pxdb\dbSchema {



	public function initFields() {
		return [
			(new dbField('user_id',  'increment')),
			(new dbField('username', 'varchar', 16))
				->setNullable(FALSE) ->setUnique(TRUE),
			(new dbField('email',    'varchar', 255))
				->setNullable(TRUE)  ->setDefault(NULL),
			(new dbField('password', 'varchar', 255))
				->setNullable(TRUE)  ->setDefault(NULL),
		];
	}



}
