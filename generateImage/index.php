<?php

// allow case insensitive parameter keys
$_GET = array_change_key_case($_GET, CASE_UPPER);

$currency='USD';
if(isset($_GET["CUR"]))
{
	$currency=$_GET["CUR"];
}

$value=1;
if(isset($_GET["VALUE"]))
{
	$value=$_GET["VALUE"];
}

$prices='last';
if(isset($_GET["TYPE"]))
{
	$prices=strtolower($_GET["TYPE"]);
	if($prices == '24h_avg')
	{
		$prices = 'averages,day';
	}
}


$homepage = file_get_contents('https://apiv2.bitcoinaverage.com/indices/global/ticker/BTC'.$currency);
$json = json_decode($homepage,true);

$timestamp="";
if(isset($_GET["TIMESTAMP"]))
{
	if(strtolower($_GET["TIMESTAMP"])=="yes")
		$timestamp=$json["display_timestamp"];
}

$precision=5;
if(isset($_GET["PRECISION"]))
{
	$precision=$_GET["PRECISION"];
}

$color='000000';
if(isset($_GET["COLOR"]))
{
	$color=$_GET["COLOR"];
}

$bgcolor='';
if(isset($_GET["BGCOLOR"]))
{
	$bgcolor=$_GET["BGCOLOR"];
}

$opacity='';
if(isset($_GET["OPACITY"]))
{
	$opacity=$_GET["OPACITY"];
}

$prices = explode(",", $prices);
$jsonPrice = $json;
foreach ($prices as $price) {
    $jsonPrice = $jsonPrice[$price];
}
$text = (round($value/doubleval($jsonPrice),$precision))." BTC  ".$timestamp;

header('Content-Type: image/png');

$size=11;
if(isset($_GET["SIZE"]))
{
	$size=$_GET["SIZE"];
}

// Here can be problem on some servers. Other solution is font + .ttf and delete putenv('GDFONTPATH=' . realpath('.'));
putenv('GDFONTPATH=' . realpath('.'));
$font = 'arial';
if(isset($_GET["FONT"]))
{
	$font=$_GET["FONT"];
}

$bbox = imagettfbbox($size, 0, $font, $text);

$im = imagecreate(12+$bbox[2]-$bbox[0], $size+2);

// Create some colors
$bg = imagecolorallocatealpha($im,255,255,255,127);
$white = imagecolorallocate($im, 255, 255, 255);
$black = imagecolorallocate($im, 0, 0, 0);

if ( strlen($color) == 6 && preg_match('/[0-9a-fA-F]{6}/', $color) ) {
                $black = imagecolorallocate($im, hexdec($color[0] . $color[1]),hexdec($color[2] . $color[3]),hexdec($color[4] . $color[5]));
        }
if ( strlen($bgcolor) == 6 && preg_match('/[0-9a-fA-F]{6}/', $bgcolor) ) {
				if(strlen($opacity) > 0 && intval($opacity) >= 0 && intval($opacity) <= 127){
					$bg = imagecolorallocatealpha($im, hexdec($bgcolor[0] . $bgcolor[1]),hexdec($bgcolor[2] . $bgcolor[3]),hexdec($bgcolor[4] . $bgcolor[5]),$opacity);
				} else {
					$bg = imagecolorallocate($im, hexdec($bgcolor[0] . $bgcolor[1]),hexdec($bgcolor[2] . $bgcolor[3]),hexdec($bgcolor[4] . $bgcolor[5]));
				}
        }
		
imagefill($im, 0, 0, $bg);
imagesavealpha($im,true);
imagefilledrectangle($im, 0, 0, 12+$bbox[2]-$bbox[0], $size+2, $bg);

imagettftext($im, $size, 0, 10, $size+1, $black, $font, $text);

imagepng($im);
imagedestroy($im);


?> 
