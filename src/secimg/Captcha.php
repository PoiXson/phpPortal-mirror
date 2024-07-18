<?php declare(strict_types=1);
/*
 * PoiXson phpPortal - Website Utilities Library
 * @copyright 2004-2024
 * @license AGPL-3
 * @author lorenzo at poixson.com
 * @link https://poixson.com/
 */
namespace pxn\phpPortal\secimg;

use \pxn\phpUtils\utils\ImageUtils;


class Captcha {

	public string $font_path  = '';
	public int    $font_count = 9;

	public ?string $phrase = null;

	public int $width  = 200;
	public int $height =  60;

	public int $angle_max  = 20;
	public int $offset_max = 10;

	public array   $backgrounds = [];
	public ?array  $color_bg    = null;
	public ?array  $color_text  = null;
	public ?string $font        = null;



	public function __construct() {
		ImageUtils::RequireGD();
		$this->font_path = __DIR__.'/fonts';
	}



	public function build(): \GdImage {
		$img = null;
		// fill background color
		if (empty($this->backgrounds)) {
			$img = \imagecreatetruecolor($this->width, $this->height);
			$bg = null;
			if (empty($this->color_bg)) $bg = \imagecolorallocate($img, \rand(200, 255), \rand(200, 255), \rand(200, 255));
			else                        $bg = \imagecolorallocate($img, $this->color_bg[0], $this->color_bg[1], $this->color_bg[2]);
			\imagefill($img, 0, 0, $bg);
		// background from image
		} else {


		}
		// effects

		// text
		if (empty($this->phrase)) {
			$this->phrase = '';
			$letters = 'abcdefghijklmnopqrstuvwxyz';
			for ($i=0; $i<6; $i++) {
				$rnd = \rand(0, \mb_strlen($letters));
				$this->phrase .= \mb_substr($letters, $rnd, 1);
			}
		}
		if (empty($this->font))
			$this->font = $this->font_path.'/captcha-'.\rand(1, $this->font_count).'.ttf';
		if (!\is_file($this->font))
			throw new \Exception('Captcha font not found: '.$this->font);
		$len = \mb_strlen($this->phrase);
		$size = ((int)\round($this->width / $len)) - \rand(0, 8) + 4;
		$box = \imagettfbbox($size, 0, $this->font, $this->phrase);
		$text_width  = ($box[2] - $box[0]) + $len;
		$text_height =  $box[1] - $box[7];
		$x = (int) \round( ($this->width  - $text_width ) / 2.0 );
		$y = ((int) \round( $this->height / 2.0 )) + 10;
		$col = null;
		if (empty($this->color_text)) {
			$this->color_text = [
				\rand(0, 150),
				\rand(0, 150),
				\rand(0, 150),
			];
		}
		$col = \imagecolorallocate($img, $this->color_text[0], $this->color_text[1], $this->color_text[2]);
		for ($i=0; $i<$len; $i++) {
			$chr = \mb_substr($this->phrase, $i, 1);
			$box = \imagettfbbox($size, 0, $this->font, $chr);
			$w = $box[2] - $box[0];
			$angle  = \rand(0-$this->angle_max,  $this->angle_max );
			$offset = \rand(0-$this->offset_max, $this->offset_max);
			\imagettftext($img, $size, $angle, $x, $y+$offset, $col, $this->font, $chr);
			$x += $w + 2;
		}
		// effects

		// distort

		// post-effects

		return $img;
	}



}
