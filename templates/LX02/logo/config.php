<?php
/**
 * Logo Configuration File
 *
 * This is a PHP file that sets up variables specific for a template.
 * It can also be used to run PHP code for a template.
 *
 */
class Nightwear_logo extends LogoMaker
{
	/**
	 * @var int The number of configurable text fields this logo has.
	 */
	public $TextFieldCount = 3;

	/**
	 * @var string The type of file (extention) the logo maker should use. gif, png or jpg
	 */
	public $FileType = 'png';

	/**
	 * @var array Array of text fields for this logo.
	 */
	public $Text = array(
		'Sleep',
		'Well',
		'Nightwear'
	);

	/**
	 * Generate a logo.
	 *
	 * @return string The generated logo.
	 */
	public function GenerateLogo()
	{
		$this->FontPath = dirname(__FILE__) . '/fonts/';
		$this->ImagePath = dirname(__FILE__) . '/';
		$this->SetBackgroundImage('back.png');
		$size = getimagesize(dirname(__FILE__)."/back.png");
		$imageHeight = $size['1'];
		$imageWidth = 0;

		if(strlen($this->Text[0])) {
			$textPosition = $this->AddText($this->Text[0], 'NuevaStd-Regular.otf', '3dc1ff', 54, 59, 59);
			$imageWidth = max($imageWidth, $textPosition['top_right_x']);
		}

		if(strlen($this->Text[1])) {
			$textPosition = $this->AddText($this->Text[1], 'NuevaStd-Regular.otf', 'ffffff', 54, $imageWidth+15, 59);
			$imageWidth = max($imageWidth, $textPosition['top_right_x']);
		}

		if(strlen($this->Text[2])) {
			$this->Text[2] = preg_replace('#.{1}#', '$0 ', $this->Text[2]);
			$right = $textPosition['top_right_x'] - (strlen($this->Text[2])*7.3);
			$textPosition = $this->AddText(strtoupper($this->Text[2]), 'Arial.ttf', 'ffffff', 12, $right, 100);
			$imageWidth = max($imageWidth, $textPosition['top_right_x']);
		}

		$this->SetImageSize($imageWidth+20, $imageHeight);
		$this->CropImage = true;
		return $this->MakeLogo();
	}
}