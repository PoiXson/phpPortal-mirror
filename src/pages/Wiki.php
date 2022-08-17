<?php declare(strict_types=1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2022
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal\pages;

use \League\CommonMark\MarkdownConverter;
use \League\CommonMark\Extension as ComMarkExt;


abstract class Wiki extends \pxn\phpPortal\Page {



	public static function getMarkdownConverter(): MarkdownConverter {
		$cfg = [
			'external_link' => [
				'internal_hosts' => 'offgrid.local',
				'open_in_new_window' => true,
				'html_class' => 'external-link',
				'nofollow'   => '',
				'noopener'   => '',
				'noreferrer' => '',
			]
		];
		$env = new \League\CommonMark\Environment\Environment($cfg);
		$env->addExtension( new ComMarkExt\CommonMark\CommonMarkCoreExtension()       );
		$env->addExtension( new ComMarkExt\Attributes\AttributesExtension()           );
		$env->addExtension( new ComMarkExt\DescriptionList\DescriptionListExtension() );
		$env->addExtension( new ComMarkExt\ExternalLink\ExternalLinkExtension()       );
		$env->addExtension( new ComMarkExt\Autolink\AutolinkExtension()               );
		$env->addExtension( new ComMarkExt\Strikethrough\StrikethroughExtension()     );
		$env->addExtension( new ComMarkExt\Table\TableExtension()                     );
		$env->addExtension( new ComMarkExt\TaskList\TaskListExtension()               );
		$converter = new MarkdownConverter($env);
		return $converter;
	}



	public function __construct(\pxn\phpPortal\WebApp $app) {
		parent::__construct($app);
	}



	public function render(): void {
		$converter = self::getMarkdownConverter();
//TODO:
		$file = $this->app->paths['data'].'/wiki/home.txt';
		$content = \file_get_contents($file);
		if ($content === false)
			throw new \RuntimeException('Failed to load wiki file: '.$file);
		$rendered_content = $converter->convertToHtml($content);
		$twig = $this->getTwig();
		$tags = [
			'body' => 'wiki.twig',
			'content' => $rendered_content,
		];
		echo $twig->render('main.twig', $tags);
	}



}
