<?php
/*
 * GrowControl Website
 * @copyright 2004-2016
 * @author lorenzo at poixson.com
 * @link http://growcontrol.com/
 */
namespace pxn\phpPortal\schemas;

use pxn\pxdb\dbField;


class table_comments extends \pxn\pxdb\dbTableSchema {



	public function initFields() {
		$this->fields = [
			(new dbField('comment_id', 'increment')),
			(new dbField('context',    'varchar', 16))
				->setNullable(TRUE)  ->setDefault(NULL),
			(new dbField('context_id', 'int', 11))
				->setNullable(FALSE) ->setDefault(0),
			(new dbField('body',       'text'))
				->setNullable(FALSE) ->setDefault(''),
			(new dbField('author',     'varchar', 32))
				->setNullable(TRUE)  ->setDefault(NULL),
			(new dbField('timestamp',  'datetime'))
				->setNullable(FALSE),
//				->setDefault('0000-00-00 00:00:00'),
		];
	}



}
