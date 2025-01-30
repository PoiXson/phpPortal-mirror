<?php declare(strict_types=1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2025
 * @license AGPLv3+ADD-PXN-V1
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal\pages;

use \League\CommonMark\MarkdownConverter;
use \League\CommonMark\Extension as ComMarkExt;

use \pxn\phpUtils\xPaths;
use \pxn\phpUtils\utils\PathUtils;
use \pxn\phpUtils\Debug;


abstract class Wiki extends \pxn\phpPortal\Page {

	protected string $wiki_name = '';
	protected string $wiki_file = '';



	public static function getMarkdownConverter(string $wiki_name=''): MarkdownConverter {
		$cfg = [
//			'internal_link' => [
//				'base_uri' => "/",
//				'base_uri' => "/$wiki_name",
//			],
			'external_link' => [
				'internal_hosts' => 'offgrid.local',
				'open_in_new_window' => true,
				'html_class' => 'external-link',
				'nofollow'   => '',
				'noopener'   => '',
				'noreferrer' => '',
			],
			'table' => [
				'wrap' => [
					'enabled' => true,
					'tag'     => 'div',
					'attributes' => [ 'class' => 'table-responsive wiki-table' ],
				],
			],
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
//TODO
//		$env->addExtension( new \pxn\phpPortal\pages\wiki\InternalLinkExtension()     );
//		$env->addExtension( new \pxn\phpPortal\pages\wiki\BracketLinkExtension()      );
		$converter = new MarkdownConverter($env);
		return $converter;
	}



	public function getWikiName(): string {
		return (empty($this->wiki_name) ? $this->getPageName() : $this->wiki_name);
	}

	public function getFile(): string {
		if (empty($this->wiki_file)) {
			$args = $this->app->args;
			if (isset($args[0]))
				unset($args[0]);
			// content file
			$file = \implode('/', $args);
			if (empty($file))
				$file = $this->getDefaultFile();
			$file = \implode('/', [
				xPaths::get('data'),
				$this->getWikiName(),
				$file,
			]);
			if (!\str_ends_with($file, '.txt'))
				$file .= '.txt';
			// check safe path
			$file = PathUtils::NormPath($file);
			if (!\str_starts_with($file, xPaths::get('data')))
				throw new \RuntimeException("Invalid wiki path: $file");
			$this->wiki_file = $file;
		}
		return $this->wiki_file;
	}

	public function getDefaultFile(): string {
		return 'home';
	}



	public function render(): string {
		$converter = self::getMarkdownConverter();
		$file = $this->getFile();
		$content = \file_get_contents($file);
		if ($content === false)
			throw new \RuntimeException("Failed to load wiki file: $file");
		$twig = $this->getTwig();
		$tags = $this->getTags();
		$tags['content'] = $converter->convertToHtml($content);
		return $twig->render('wiki.twig', $tags);
	}



}
