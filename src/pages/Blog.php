<?php
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpPortal\pages;

use pxn\phpPortal\Website;


abstract class Blog extends \pxn\phpPortal\Page {

	protected $pool = NULL;
	protected $queries = NULL;



	public function __construct() {
		parent::__construct();
	}



	public function getPageContents() {

//$cacher = new \pxn\phpUtils\cache\cacher_filesystem();
//$value = $cacher->getEntry(
//	function () {
//		return \date('l jS \of F Y h:i:s A');
//	},
//	'test',
//	11
//);
//dump($value);
//fail('BLOG');

//$pool = \pxn\phpUtils\pxdb\dbPool::getPool();
//$pool->UpdateTables();

//self::UpdateCommentCounts(); exit(1);

		$tpl = $this->getBlogTpl();
		$website = Website::get();
		$entryId = (int) $website->getArg(1);
		$queries = $this->getQueriesClass();
		$paginate = $queries->getPaginate($pageNum, $perPage);
		if ($paginate === NULL) {
			fail('Failed to get blog paginate!');
			exit(1);
		}
		$entries  = $queries->getEntries($entryId);
		if ($entries === NULL) {
			fail('Failed to get blog entries!');
			exit(1);
		}
		$comments = $this->getQueriesClass()
			->getComments($entryId);
		return $tpl->render([
			'singleId' => (int) $entryId,
			'entries'  => $entries,
			'comments' => $comments,
			'paginate' => $paginate
		]);
	}



	public function getBlogTpl() {
		return $this->getTpl('blog');
	}
	public function getQueriesClass() {
		if ($this->queries == NULL) {
			$this->queries = new Blog_Queries($this->pool);
		}
		return $this->queries;
	}



}
