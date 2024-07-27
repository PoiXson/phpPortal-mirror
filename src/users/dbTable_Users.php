<?php declare(strict_types=1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2024
 * @license AGPL-3
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
			->name('username')
			->type(dbFieldType::TYPE_STR)
			->size(32)
			->unique()
			->primary()
		);
		$this->addField((new dbFieldFactory())
			->name('password')
			->type(dbFieldType::TYPE_STR)
			->size(255)
		);
		$this->addField((new dbFieldFactory())
			->name('email')
			->type(dbFieldType::TYPE_STR)
			->size(255)
		);
	}



}
