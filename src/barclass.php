<?php
/**
 * ***************************
 *	Bar Generator
 * 	By Mathew Collins
 * 	mathew.collins@gmail.com	http://onovia.com
 * 
 *     This class is available free of charge for personal or non-profit works. If
 *     you are using it in a commercial setting, please contact the author for license
 *     information.
 *     This class is provided as is, without guarantee. You are free to modify
 *     and redistribute this code, provided that the original copyright remain in-tact.
 * 
 *******************************/

class barGen
{
	
	public $bar_w;
 public $bar_h;
 public $fontSize;
 public $cr;
 public $cg;
 public $value;
 public $fill_color;
 public $backColor;
 public $max;
 /**
  * @var int
  */
 public $dataPercent;
 public $bar;
 public $barPercent;
 function setWidth($value): void
	{
		$this->bar_w = $value;
	}

	function setHeight($value): void
	{
		$this->bar_h = $value;
	}

	function setFontSize($value): void
	{
		$this->fontSize = $value;
	}

	function setFillColor($cr, $cg, $value): void
	{
		$this->cr = $cr;
		$this->cg = $cg;
		$this->value = $value;

		$this->fill_color = imagecolorallocate($this->bar, $this->cr, $this->cg, $this->value);
	}

	
	function setBackColor(): void
	{
		$this->backColor = imagecolorallocate($this->bar, 171, 171, 179);
	}

	function setData($max, $value): void
	{
		$this->max = $max;
		$this->value = $value;
		
		$this->dataPercent = (int) ($this->value / $this->max * 100);
	}
	
	function makeBar(): void
	{
		$this->bar = imagecreate($this->bar_w, $this->bar_h);
		$this->setBackColor();
	}

	function generateBar(): void
	{
		header('Content-type: image/png');

		$text = $this->value . " / " . $this->max;

		$white 	= imagecolorallocate($this->bar, 255, 255, 255);
		$grey 	= imagecolorallocate($this->bar, 120, 120, 120);
		
		// Background
		imagefill($this->bar, 0, 0, $this->backColor);
		// Fill
		$this->barPercent = $this->bar_w / 100 * $this->dataPercent;
		imagefilledrectangle($this->bar, 0, 0, $this->barPercent, $this->bar_h, $this->fill_color);
		// Border
		imagerectangle($this->bar, 0, 0, $this->bar_w - 1, $this->bar_h - 1, $grey);
		// Text
		imagestring($this->bar, $this->fontSize, round(($this->bar_w/2)-((strlen($text)*imagefontwidth($this->fontSize))/2), 1), round(($this->bar_h/2)-(imagefontheight($this->fontSize)/2)), $text, $white);
		// Output
		imagepng($this->bar);
		imagedestroy($this->bar);

	}
}