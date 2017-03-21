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
			(new dbField('entry_id',       'increment'   )),
			(new dbField('title',          'varchar', 255)),
			(new dbField('body',           'text'        )),
			(new dbField('timestamp',      'datetime'    )),
			(new dbField('author_id',      'int', 11     )),
			(new dbField('count_comments', 'int', 11     ))
		];
	}



}
