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
		$env->addExtension( new \League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension()       );
		$env->addExtension( new \League\CommonMark\Extension\Attributes\AttributesExtension()           );
		$env->addExtension( new \League\CommonMark\Extension\DescriptionList\DescriptionListExtension() );
		$env->addExtension( new \League\CommonMark\Extension\ExternalLink\ExternalLinkExtension()       );
		$env->addExtension( new \League\CommonMark\Extension\Autolink\AutolinkExtension()               );
		$env->addExtension( new \League\CommonMark\Extension\Strikethrough\StrikethroughExtension()     );
		$env->addExtension( new \League\CommonMark\Extension\Table\TableExtension()                     );
		$env->addExtension( new \League\CommonMark\Extension\TaskList\TaskListExtension()               );
		$converter = new MarkdownConverter($env);
		return $converter;
	}



	public function __construct(\pxn\phpPortal\WebApp $app) {
		parent::__construct($app);
	}



	public function render(): void {
		$twig = $this->getTwig();
		$this->addTwigExt_Markdown();
		$tags = [
			'body' => 'wiki.twig',
		];
		echo $twig->render('main.twig', $tags);
	}



}
