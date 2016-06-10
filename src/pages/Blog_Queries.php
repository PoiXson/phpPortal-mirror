<?php
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpPortal\pages;

use pxn\phpPortal\Paginate;

use pxn\phpUtils\pxdb\dbPool;
use pxn\phpUtils\pxdb\dbConn;

use pxn\phpUtils\Numbers;
use pxn\phpUtils\cache\cacher_filesystem;


class Blog_Queries {

	public $dateFormat = 'D, d M Y, H:i';

	public $pool = NULL;

	public $perPage = NULL;



	public function __construct($pool, $perPage) {
		$this->pool    = $pool;
		$this->perPage = $perPage;
	}



	public function getPaginate($pageNum) {
		$perPage   = Numbers::MinMax( (int) $this->perPage, 1, 1000);
		$pageCount = $this->getPageCount();
		if ($pageCount == NULL) {
			return NULL;
		}
		if ($pageNum > $pageCount) {
			$pageNum = $pageCount;
		}
		$paginate = Paginate::doPaginate(
			$pageNum,
			$pageCount,
			$perPage,
			2
		);
		return $paginate;
	}
	public function getPageCount() {
		$entryCount = $this->getEntryCount();
		if ($entryCount == NULL) {
			return NULL;
		}
		$perPage = Numbers::MinMax( (int) $this->perPage, 1, 1000);
		$pageCount = \ceil( ((double)$entryCount) / ((double)$perPage) );
		return $pageCount;
	}
	public function getEntryCount() {
		// load cacher
		$cacher = new cacher_filesystem(60);
		// get cache entry
		$count = $cacher->getEntry(
			// new cache entry
			function () {
				return $this->_getEntryCount();
			},
			'blog_entry_count'
		);
		if (empty($count)) {
			return NULL;
		}
		return (int) $count;
	}
	protected function _getEntryCount() {
		$db = dbPool::get($this->pool, dbConn::ERROR_MODE_EXCEPTION);
		if ($db == NULL) {
			return NULL;
		}
		$entryCount = NULL;
		$sql = '';
		try {
			$sql = $this->getEntryCountSQL();
			$db->Execute($sql);
			if (!$db->hasNext()) {
				$db->release();
				return NULL;
			}
			$entryCount = $db->getInt('count');
		} catch (\PDOException $e) {
			fail("Query failed: {$sql}", $e);
			exit(1);
		}
		$db->release();
		return $entryCount;
	}
	protected function getEntryCountSQL() {
		return "SELECT count(*) AS `count` FROM `__TABLE__blog_entries` WHERE `timestamp` <= NOW()";
	}



	public function getEntries($pageNum=NULL, $entryId=NULL) {
		$pageNum = (int) $pageNum;
		$entryId = (int) $entryId;
		$db = dbPool::get($this->pool, dbConn::ERROR_MODE_EXCEPTION);
		if ($db == NULL) {
			return NULL;
		}
		$entries = [];
		$rowNum = 0;
		$sql = '';
		try {
			$sql = $this->getEntriesSQL($pageNum, $entryId);
			$db->Execute($sql);
			while ($db->hasNext()) {
				$rowNum++;
				$timestamp = $db->getInt('timestamp');
				$timeNow = \time();
				$timeSinceStr =
					($timestamp > 0)
					? Numbers::SecondsToText($timeNow - $timestamp, FALSE, 2, 0.9)
					: '';
				$entry = [
					'rowNum'        => $rowNum,
					'id'            => $db->getInt('entry_id'),
					'title'         => $db->getString('title'),
					'body'          => $db->getString('body'),
					'dateFormatted' => \date($this->dateFormat, $timestamp),
					'timeSinceText' => $timeSinceStr,
					'commentCount'  => $db->getInt('comment_count')
				];
				$entries[] = $entry;
			}
		} catch (\PDOException $e) {
			fail("Query failed: {$sql}", $e);
			exit(1);
		}
		$db->release();
		return $entries;
	}
	protected function getEntriesSQL($pageNum=NULL, $entryId=NULL) {
		$pageNum = Numbers::MinMax( (int) $pageNum, 0);
		$perPage = Numbers::MinMax( (int) $this->perPage, 1, 1000);
		$entryId = (int) $entryId;
		$WHERE = '`timestamp` <= NOW()';
		$LIMIT = '';
		if ($entryId > 0) {
			$WHERE .= "AND `entry_id` = {$entryId}";
		} else {
			if ($pageNum < 1) {
				$pageNum = 1;
			}
			$from  = ($pageNum - 1) * $perPage;
			$LIMIT = "LIMIT {$from}, {$perPage}";
		}
		return "SELECT `entry_id`, `title`, `body`, `comment_count`, ".
			"UNIX_TIMESTAMP(`timestamp`) AS `timestamp` ".
			"FROM `__TABLE__blog_entries` ".
			"WHERE {$WHERE} ".
			"ORDER BY `timestamp` DESC, `entry_id` DESC ".
			$LIMIT;
	}



	public function getComments($entryId=NULL) {
		$entryId = (int) $entryId;
		if ($entryId <= 0) {
			return NULL;
		}
		$db = dbPool::get($this->pool, dbConn::ERROR_MODE_EXCEPTION);
		if ($db == NULL) {
			return NULL;
		}
		$comments = [];
		$commentNum = 0;
		try {
			$sql = $this->getCommentsSQL($entryId);
			$db->Prepare($sql);
			$db->setInt(':id', $entryId);
			$db->Execute();
			while ($db->hasNext()) {
				$commentNum++;
				$timestamp = $db->getInt('timestamp');
				$timeNow = \time();
				$timeSinceStr =
					($timestamp > 0)
					? Numbers::SecondsToText($timeNow - $timestamp, FALSE, 2, 0.9)
					: '';
				$comment = [
					'commentNum'    => $commentNum,
					'id'            => $db->getInt('comment_id'),
					'author'        => $db->getString('author'),
					'body'          => $db->getString('body'),
					'dateFormatted' => \date($this->dateFormat, $timestamp),
					'timeSinceText' => $timeSinceStr
				];
				$comments[] = $comment;
			}
		} catch (\PDOException $e) {
			fail("Query failed: {$sql}", $e);
			exit(1);
		}
		$db->release();
		return $comments;
	}
	protected function getCommentsSQL($entryId=NULL) {
		$entryId = (int) $entryId;
		return "SELECT `comment_id`, `body`, `author`, ".
			"UNIX_TIMESTAMP(`timestamp`) AS `timestamp` ".
			"FROM `__TABLE__comments` ".
			"WHERE `context` = 'blog' AND `context_id` = :id ".
			"ORDER BY `timestamp` DESC, `comment_id` DESC ".
			"LIMIT 1000";
	}



	public function UpdateCommentCounts($entry_id=NULL) {
		if ($entry_id !== NULL) {
			$entry_id = (int) $entry_id;
			if ($entry_id <= 0) {
				$funcName = __function__;
				fail("Invalid entry_id provided to {$funcName} function: {$entry_id}");
				exit(1);
			}
		}
		$dbQuery  = dbPool::get($this->pool, dbConn::ERROR_MODE_EXCEPTION);
		$dbUpdate = dbPool::get($this->pool, dbConn::ERROR_MODE_EXCEPTION);
		try {
			$sql = "SELECT `entry_id` FROM `__TABLE__blog_entries`";
			$dbQuery->Execute($sql);
			$count = 0;
			while ($dbQuery->hasNext()) {
				$id = $dbQuery->getInt('entry_id');
				if ($id <= 0) {
					fail('Invalid entry_id value in blog_entries table!');
					exit(1);
				}
				try {
					$sql = "UPDATE `__TABLE__blog_entries` SET ".
						"`comment_count` = ( ".
							"SELECT COUNT(*) FROM `__TABLE__comments` ".
							"WHERE `context` = 'blog' AND `context_id` = :id ".
						") ".
						"WHERE `entry_id` = :id LIMIT 1";
					$dbUpdate->Prepare($sql);
					$dbUpdate->setInt(':id', $id);
					$dbUpdate->Execute();
				} catch (\PDOException $e) {
					fail("Query failed: {$sql}", $e);
					exit(1);
				}
				$dbUpdate->clean();
				$count++;
			}
		} catch (\PDOException $e) {
			fail("Query failed: {$sql}", $e);
			exit(1);
		}
		$dbUpdate->release();
		$dbQuery->release();
	}



}
