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

	public string $bg_path  = '';
	public int    $bg_count = 9;

	public ?string $phrase = null;

	public int $width  = 240;
	public int $height =  70;

	public bool $use_bg_image = true;

	public int $angle_max  = 20;
	public int $offset_max = 10;

	public ?array  $color_bg    = null;
	public ?array  $color_text  = null;
	public ?string $font        = null;



	public function __construct() {
		ImageUtils::RequireGD();
		$this->font_path = __DIR__.'/fonts';
		$this->bg_path   = __DIR__.'/backgrounds';
	}



	public function build(): \GdImage {
		$img = null;
		// background from image
		if ($this->use_bg_image) {
			$bg = 0;
			$bg_file = $this->bg_path.'/captcha-'.\rand(1, $this->bg_count).'.png';
			$img = \imagecreatefrompng($bg_file);
		// fill background color
		} else {
			$img = \imagecreatetruecolor($this->width, $this->height);
			$bg = null;
			if (empty($this->color_bg)) $bg = \imagecolorallocate($img, \rand(100, 255), \rand(100, 255), \rand(100, 255));
			else                        $bg = \imagecolorallocate($img, $this->color_bg[0], $this->color_bg[1], $this->color_bg[2]);
			\imagefill($img, 0, 0, $bg);
		}
		// background lines
		{
			$num = \rand(8, 20);
			for ($i=0; $i<$num; $i++)
				$this->draw_line($img, 5);
		}
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
		// foreground lines
		{
			$num = \rand(2, 4);
			for ($i=0; $i<$num; $i++)
				$this->draw_line($img, 2);
		}
		// distort
		$img = $this->distort($img, $bg);
		$effect = \rand(0, 4);
		// invert
		if ($effect == 1)
			\imagefilter($img, \IMG_FILTER_NEGATE);
		// edge
		if ($effect == 2)
			\imagefilter($img, \IMG_FILTER_EDGEDETECT);
		// contrast
		\imagefilter($img, \IMG_FILTER_CONTRAST, \rand(-50, 10));
		// colorize
		if ($effect == 3)
			\imagefilter($img, \IMG_FILTER_COLORIZE, \rand(-80, 50), \rand(-80, 50), \rand(-80, 50));
		return $img;
	}



	protected function draw_line(\GdImage $img, int $thick=5): void {
		$col = null;
		for ($i=0; $i<5; $i++) {
			$col = \imagecolorallocate($img, \rand(100, 255), \rand(100, 255), \rand(100, 255));
			if ($col !== false) break;
		}
		if ($col === false) return;
		$size = ($thick>1 ? \rand(1, $thick) : 1);
		\imagesetthickness($img, $size);
		$x1 = \rand(0,              $this->width/2); $y1 = \rand(0, $this->height);
		$x2 = \rand($this->width/2, $this->width  ); $y2 = \rand(0, $this->height);
		\imageline($img, $x1, $y1, $x2, $y2, $col);
	}

	protected function distort(\GdImage $img, int $bg): \GdImage {
		$result = \imagecreatetruecolor($this->width, $this->height);
		$rx = \rand(0, $this->width);
		$ry = \rand(0, $this->height);
		$scale = (\rand(0, 10000) / 30000) + 1.1;
		$phase = \rand(0, 10);
		for ($ix=0; $ix<$this->width; $ix++) {
			for ($iy=0; $iy<$this->height; $iy++) {
				$vx = $ix - $rx;
				$vy = $iy - $ry;
				$vn = \sqrt( ($vx*$vx)+($vy*$vy) );
				if ($vn == 0) {
					$nx = $rx;
					$ny = $ry;
				} else {
					$v = $vn + (4 * \sin($vn / 30));
					$nx = $rx + (($vx * $v) / $vn);
					$ny = $ry + (($vy * $v) / $vn);
				}
				$ny += $scale * \sin($phase + ($nx*0.2));
				$p = $this->interpolate(
					(int) ($nx - \floor($nx)),
					(int) ($ny - \floor($ny)),
					$this->get_color($img, (int)\floor($nx), (int)\floor($ny), $bg),
					$this->get_color($img, (int)\ceil ($nx), (int)\floor($ny), $bg),
					$this->get_color($img, (int)\floor($nx), (int)\ceil ($ny), $bg),
					$this->get_color($img, (int)\ceil ($nx), (int)\ceil ($ny), $bg)
				);
				if ($p == 0)
					$p = $bg;
				\imagesetpixel($result, $ix, $iy, $p);
			}
		}
		return $result;
	}

	protected function interpolate(int $x, int $y, int $ne, int $nw, int $se, int $sw): int {
		list($r1, $g1, $b1) = $this->get_rgb($ne);
		list($r0, $g0, $b0) = $this->get_rgb($nw);
		list($r3, $g3, $b3) = $this->get_rgb($se);
		list($r2, $g2, $b2) = $this->get_rgb($sw);
		$cx = 1.0 - $x;
		$cy = 1.0 - $y;
		$m0 = ($cx * $r0) + ($x * $r1);
		$m1 = ($cx * $r2) + ($x * $r3);
		$r  = (int) (($cy * $m0) + ($y * $m1));
		$m0 = ($cx * $g0) + ($x * $g1);
		$m1 = ($cx * $g2) + ($x * $g3);
		$g  = (int) (($cy * $m0) + ($y * $m1));
		$m0 = ($cx * $b0) + ($x * $b1);
		$m1 = ($cx * $b2) + ($x * $b3);
		$b  = (int) (($cy * $m0) + ($y * $m1));
		return ($r<<16) | ($g<<8) | $b;
	}



	protected function get_color(\GdImage $img, int $x, int $y, int $bg): int {
		$low  = \imagesx($img);
		$high = \imagesy($img);
		if ($x<0 || $x>=$low
		||  $y<0 || $y>=$high)
			return $bg;
		return \imagecolorat($img, $x, $y);
	}
	protected function get_rgb(int $col): array {
		return [
			(int) ($col >> 16) & 0xff,
			(int) ($col >>  8) & 0xff,
			(int) ($col      ) & 0xff,
		];
	}



}
