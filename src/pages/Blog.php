<?php
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpPortal\pages;

use pxn\phpUtils\pxdb\dbPool;
use pxn\phpUtils\pxdb\dbConn;

use pxn\phpUtils\Numbers;


abstract class Blog extends \pxn\phpPortal\Page {

	protected static $dbName = 'main';

	protected $dateFormat = \DATE_RFC2822;



	public function getPageContents() {
		$tpl = $this->getBlogTpl();
		$entries = $this->getEntries();
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



	protected function getEntries() {
		$db = $this->doQuery();
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
	protected function doQuery() {
		$db = dbPool::get(self::$dbName, dbConn::ERROR_MODE_EXCEPTION);
		if ($db == NULL) {
			return NULL;
		}
		try {
			$sql = $this->getSql();
			$db->Prepare($sql);
			$db->Execute();
		} catch (\PDOException $e) {
			fail("Query failed: {$sql}", $e);
			exit(1);
		}
		return $db;
	}
	protected function getSql() {
		return "SELECT `entry_id`, `title`, `body`, `comment_count`, ".
			"UNIX_TIMESTAMP(`timestamp`) AS `timestamp` ".
//			"( SELECT COUNT(*) FROM `__TABLE__comments` ".
//				"WHERE `context` = 'blog' AND ".
//				"`context_id` = `__TABLE__blog_entries`.`entry_id` ".
//				") AS `comment_count` ".
			"FROM `__TABLE__blog_entries` ".
			"WHERE `timestamp` <= NOW() ".
			"ORDER BY `timestamp` DESC, `entry_id` DESC ".
			"LIMIT 5";
	}



}
