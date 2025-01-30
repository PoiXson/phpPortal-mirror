<?php declare(strict_types=1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2025
 * @license AGPLv3+ADD-PXN-V1
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal\users;

use \pxn\pxdb\dbFieldFactory;
use \pxn\pxdb\dbFieldType;


class dbTable_Users extends \pxn\pxdb\dbTable {



	public function __construct() {
		parent::__construct('users');
	}



	protected function initFields(): void {
		$this->addField((new dbFieldFactory())
			->name('user')
			->type(dbFieldType::TYPE_STR)
			->size(32)
			->unique()
			->primary()
		);
		$this->addField((new dbFieldFactory())
			->name('pass')
			->type(dbFieldType::TYPE_STR)
			->size(255)
		);
		$this->addField((new dbFieldFactory())
			->name('secret')
			->type(dbFieldType::TYPE_STR)
			->size(32)
			->defval('')
		);
		$this->addField((new dbFieldFactory())
			->name('email')
			->type(dbFieldType::TYPE_STR)
			->size(255)
			->defval('')
		);
	}



}
