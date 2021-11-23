<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * @package     Joomla.Administrator
 * @subpackage  com_edamame
 *
 * @copyright   (C) 2021 - Alison Keen 
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 *
 * THIS CODE IS TOTALLY BROKEN and needs rewriting, 
 * i obviously started it and just haven't come back to it yet
 * 
 * Quick overview to jog my memory:
 * 1, read the likely variables from last time we submitted the form
 * 2, check for values we need to feed back to database, put into DB
 * 3, then read new status from database and display
 */


use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;

JViewLegacy::loadHelper('edamamehelper');

$jinput = JFactory::getApplication()->input;

// need to define some objects so they have whole-page scope and can be accessed;
$related_bill = null;
$a_quote = null;

## BROKEN needs to use the HElper::getDB function... 
$db = JDatabaseDriver::getInstance( $option );



// ========== End of under-the-hood processing, start of displaying page ==========

$dimensions = "width = 200px; height = 200px;";

echo "<h2>Artwork Generator Page</h2>";

$imagename = "green mini tile.png";

$image_location = Uri::root() . "media/com_edamame/tiles/";

$imagestr = HTMLHelper::image( $image_location . $imagename , 'No Data', $dimensions);

echo $imagestr; 

$silhouettes_location = Uri::root() . "media/com_edamame/silhouettes/";

$shortsleeve = "dscn-mens-SS.jpeg";

$longsleeve = "dscn-mens-LS-pocket.jpeg";

echo HTMLHelper::image( $silhouettes_location . $shortsleeve , 'DSCN Short Sleeve Shirt' , $dimensions);

echo "\n<h4> Panels to generate: </h4>"; 

$panels = array ( 
  array("Name" => "Front Right", "x" => 1433, "y" => 3666),
  array("Name" => "Front Left", "x" => 1433, "y" => 3666),
  array("Name" => "Sleeve Left", "x" => 2069, "y" => 1321),
  array("Name" => "Back", "x" => 2777, "y" => 3150),
  array("Name" => "Back Top", "x" => 2617, "y" => 827 ),
  array("Name" => "Sleeve Right", "x" => 2078, "y" => 1321),
  array("Name" => "Placket", "x" => 427, "y" => 2600),
  array("Name" => "Collar", "x" => 2073 , "y" => 414 ),
  array("Name" => "Collar Inside", "x" => 2082, "y" => 260 )
);

foreach ($panels as $panel) {

  echo "\n" . $panel['Name'] . ": " . $panel['x'] . "px by " . $panel['y'] . "px <br/>";
  //print_r($panel);

}

$tile = imagecreatefromPNG($image_location . $imagename);

if(!($newpanel = imagecreatetruecolor(200, 200))) {
  echo "\n<br/> <h3> Couldn't create image for some reason... </h3>";
  die();
}

imagesettile($newpanel, $tile);

imagefilledrectangle($newpanel, 0, 0, 199, 199, IMG_COLOR_TILED);

echo "\n<br/>CWD: " . getcwd() . "\n<br/>"; 

$adjust = "../";
$outputlocation = "media/com_edamame/generated/";
$filename = "panel3.png";

$dest = $adjust . $outputlocation . $filename;

if(imagePNG($newpanel, $dest)) {
  echo "Saved: " . $dest;
}else {
  echo "Didn't save to " . $dest . " ... sorry."; 
}

//echo "\n</br> Created Image: </br>";

$location = URI::root() . $outputlocation . $filename;

//echo HTMLHelper::image($location , 'panel' , $dimensions);

echo "\n</br>Stars:  </br>";


$file_path =  URI::root() . $outputlocation;
$size = 200;
$stars = 4;
$value = 2;
$filename2 = 'starsimage.png';

EdamameHelper::generate_png($file_path, $size, $stars, $value, $filename)

$outputfile = URI::root() . $outputlocation . $filename2;


echo HTMLHelper::image($outputfile , 'stars' , $dimensions);

echo "\n<h4> Preeeetty. </h4>";

?>

