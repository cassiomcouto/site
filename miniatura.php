<?php
// Get extension and return it
$file = isset($_GET["img"])? $_GET["img"] : '';
$width = isset($_GET["w"])? (int)$_GET["w"] : 70;
$sold = isset($_GET["sold"])? 1 : 0;

if (!empty($file)) {
	$ext = pathinfo($file, PATHINFO_EXTENSION);
	header("Content-type: image/$ext");
} else {
	exit();
}

function openImage($file)
{
	// Get extension and return it
	$ext = pathinfo($file, PATHINFO_EXTENSION);
	switch(strtolower($ext)) {
		case 'jpg':
		case 'jpeg':
			$im = @imagecreatefromjpeg($file);
			break;
		case 'gif':
			$im = @imagecreatefromgif($file);
			break;
		case 'png':
			$im = @imagecreatefrompng($file);
			break;
		default:
			$im = false;
			break;
	}
	return $im;
}

$im = '';
$im = openImage($file);

if (!empty($im)) {
	$old_x = imageSX($im);
    $old_y = imageSY($im);

    $new_w = (int)($width);

	if (($new_w <= 0) or ($new_w>$old_x)) {
		$new_w=$old_x;
    }

    $new_h = ($old_x*($new_w/$old_x));

    if ($old_x > $old_y) {
        $thumb_w = $new_w;
        $thumb_h = $old_y*($new_h/$old_x);
    }
    if ($old_x < $old_y) {
        $thumb_w = $old_x*($new_w/$old_y);
        $thumb_h = $new_h;
    }
    if ($old_x == $old_y) {
		$thumb_w = $new_w;
		$thumb_h = $new_h;
    }

	$thumb = imagecreatetruecolor($thumb_w,$thumb_h);

	if ($sold == 1) {
        $watermark_img = openImage('images/watermark.png');
        $watermark_width = imagesx($watermark_img);
        $watermark_height = imagesy($watermark_img);

        $watermark = imagecreatetruecolor($old_x,$old_y);
		imagecopyresampled($watermark,$watermark_img,0,0,0,0,$old_x,$old_y,$watermark_width,$watermark_height);
	    imagedestroy($watermark_img);

		imagecopymerge($im, $watermark, 0, 0, 0, 0, $old_x, $old_y, 30);
    	imagedestroy($watermark);
    }

	imagecopyresampled($thumb,$im,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y);

	//choose which image program to use
	if ($ext == 'jpeg' || 'jpg') {
		imagejpeg($thumb);
	} elseif ($ext == 'png') {
		imagepng($thumb);
	} elseif ($ext == 'gif') {
		imagegif($thumb);
	}
    imagedestroy($thumb);
    imagedestroy($im);
}
?>