<?php
/*
 * GrowControl Website
 * @copyright 2004-2016
 * @author lorenzo at poixson.com
 * @link http://growcontrol.com/
 */
namespace pxn\phpPortal\schemas;

use pxn\pxdb\dbField;


class table_blog_entries extends \pxn\pxdb\dbTableSchema {



	public function initFields() {
		$this->fields = [
			(new dbField('entry_id',       'increment')),
			(new dbField('title',          'varchar', 255))
				->setNullable(FALSE) ->setDefault(''),
			(new dbField('body',           'text'))
				->setNullable(FALSE) ->setDefault(''),
			(new dbField('timestamp',      'datetime'))
				->setNullable(FALSE),
//				->setDefault('0000-00-00 00:00:00'),
			(new dbField('author_id',      'int', 11))
				->setNullable(FALSE) ->setDefault(0),
			(new dbField('count_comments', 'int', 11))
				->setNullable(FALSE) ->setDefault(0),
		];
	}



}
