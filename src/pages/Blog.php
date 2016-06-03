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

use pxn\phpUtils\pxdb\dbPool;
use pxn\phpUtils\pxdb\dbConn;

use pxn\phpUtils\Numbers;


abstract class Blog extends \pxn\phpPortal\Page {

	protected static $dbName = 'main';

	protected $dateFormat = \DATE_RFC2822;



	public function getPageContents() {
		$tpl = $this->getBlogTpl();
		$entryId = (int) Website::get()->getArg(1);
		if ($entryId <= 0) {
			$entryId = NULL;
		}
		$entries = $this->getEntries($entryId);
		if ($entries == NULL) {
			fail('Failed to get blog entries!');
			exit(1);
		}
		return $tpl->render([
			'entries' => $entries
		]);
	}



	public function getBlogTpl() {
		return $this->getTpl('blog');
	}



	protected function getEntries($entryId=NULL) {
		$db = $this->doQuery($entryId);
		if ($db == NULL) {
			return NULL;
		}
		$entries = [];
		$rowNum = 0;
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
		$db->release();
		return $entries;
	}
	protected function doQuery($entryId=NULL) {
		$db = dbPool::get(self::$dbName, dbConn::ERROR_MODE_EXCEPTION);
		if ($db == NULL) {
			return NULL;
		}
		try {
			$sql = $this->getSql($entryId);
			$db->Prepare($sql);
			$db->Execute();
		} catch (\PDOException $e) {
			fail("Query failed: {$sql}", $e);
			exit(1);
		}
		return $db;
	}
	protected function getSql($entryId=NULL) {
		$entryId = (int) $entryId;
		return "SELECT `entry_id`, `title`, `body`, `comment_count`, ".
			"UNIX_TIMESTAMP(`timestamp`) AS `timestamp` ".
//			"( SELECT COUNT(*) FROM `__TABLE__comments` ".
//				"WHERE `context` = 'blog' AND ".
//				"`context_id` = `__TABLE__blog_entries`.`entry_id` ".
//				") AS `comment_count` ".
			"FROM `__TABLE__blog_entries` ".
			"WHERE `timestamp` <= NOW() ".
			( $entryId > 0 ? "AND `entry_id` = {$entryId} " : '' ).
			"ORDER BY `timestamp` DESC, `entry_id` DESC ".
			"LIMIT 5";
	}



	public static function UpdateCommentCounts($entry_id=NULL) {
		if ($entry_id !== NULL) {
			$entry_id = (int) $entry_id;
			if ($entry_id <= 0) {
				$funcName = __function__;
				fail("Invalid entry_id provided to {$funcName} function: {$entry_id}");
				exit(1);
			}
		}
		$dbQuery  = dbPool::get(self::$dbName, dbConn::ERROR_MODE_EXCEPTION);
		$dbUpdate = dbPool::get(self::$dbName, dbConn::ERROR_MODE_EXCEPTION);
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
