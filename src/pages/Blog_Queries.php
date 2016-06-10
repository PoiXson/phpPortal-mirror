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


class Blog_Queries {

	public $dateFormat = \DATE_RFC2822;

	public $pool = NULL;



	public function __construct($pool) {
		if ($pool != NULL) {
			$this->pool = $pool;
		}
	}



	public function getPaginate($pageNum, $perPage=5) {
		$perPage = Numbers::MinMax( (int) $perPage, 1, 1000);
		$db = dbPool::get($this->pool, dbConn::ERROR_MODE_EXCEPTION);
		if ($db == NULL) {
			return NULL;
		}
		$paginate = [];
		$sql = '';
		try {
			$sql = $this->getPaginateSQL();
			$db->Execute($sql);
			if (!$db->hasNext()) {
				return NULL;
			}
			$records = $db->getInt('count');
			$pageCount = \ceil( ((double)$records) / ((double)$perPage) );
			if ($pageNum > $pageCount) {
				$pageNum = $pageCount;
			}
			$paginate = Paginate::doPaginate(
				$pageNum,
				$pageCount,
				$perPage,
				2
			);
		} catch (\PDOException $e) {
			fail("Query failed: {$sql}", $e);
			exit(1);
		}
		$db->release();
		return $paginate;
	}
	protected function getPaginateSQL() {
		return "SELECT count(*) AS `count` FROM `__TABLE__blog_entries` WHERE `timestamp` <= NOW()";
	}



	public function getEntries($entryId=NULL) {
		$entryId = (int) $entryId;
		$db = dbPool::get($this->pool, dbConn::ERROR_MODE_EXCEPTION);
		if ($db == NULL) {
			return NULL;
		}
		$entries = [];
		$rowNum = 0;
		try {
			$sql = $this->getEntriesSQL($entryId);
			$db->Prepare($sql);
			if ($entryId > 0) {
				$db->setInt(':id', $entryId);
			}
			$db->Execute();
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
	public function getEntriesSQL($entryId=NULL) {
		$entryId = (int) $entryId;
		return "SELECT `entry_id`, `title`, `body`, `comment_count`, ".
			"UNIX_TIMESTAMP(`timestamp`) AS `timestamp` ".
//			"( SELECT COUNT(*) FROM `__TABLE__comments` ".
//				"WHERE `context` = 'blog' AND ".
//				"`context_id` = `__TABLE__blog_entries`.`entry_id` ".
//				") AS `comment_count` ".
			"FROM `__TABLE__blog_entries` ".
			"WHERE `timestamp` <= NOW() ".
			( $entryId > 0 ? "AND `entry_id` = :id " : '' ).
			"ORDER BY `timestamp` DESC, `entry_id` DESC ".
			"LIMIT 5";
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
	public function getCommentsSQL($entryId=NULL) {
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
