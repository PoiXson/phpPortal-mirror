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

use \pxn\phpUtils\xPaths;
use \pxn\phpUtils\utils\PathUtils;


abstract class Wiki extends \pxn\phpPortal\Page {

	protected  string $wiki_name = '';
	protected ?string $wiki_file = null;



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



	public function __construct(\pxn\phpPortal\WebApp $app, string $wiki_name='') {
		parent::__construct($app);
		$this->wiki_name = $wiki_name;
	}



	public function getDefaultPage(): string {
		return 'home';
	}

	public function getFile(): string {
		if (empty($this->wiki_file)) {
			$file = \implode('/', $this->app->args);
			// default
			if (empty($file))
				$file = $this->getDefaultPage();
			$file = \implode('/', [
				xPaths::get('data'),
				$this->wiki_name,
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

	public function isValidPage(): bool {
		return \is_file( $this->getFile() );
	}



	public function render(): void {
		$converter = self::getMarkdownConverter();
		$file = $this->getFile();
		$content = \file_get_contents($file);
		if ($content === false)
			throw new \RuntimeException("Failed to load wiki file: $file");
		$rendered_content = $converter->convertToHtml($content);
		$twig = $this->getTwig();
		$tags = [
			'content' => $rendered_content->getContent(),
		];
		$page_content = $twig->render('wiki.twig', $tags);
		echo $this->render_main($page_content);
	}



}
