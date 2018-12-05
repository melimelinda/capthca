<?php
//Generate Reference ID
if (isset($HTTP_GET_VARS["refid"]) &&
$HTTP_GET_VARS["refid"]!="") {
$referenceid = stripslashes($HTTP_GET_VARS["refid"]);
} else {
$referenceid = md5(mktime()*rand());
}
//Select Font
$font = "trebuc.ttf";
//Select random background image
$bgurl = rand(1, 3);
$im = ImageCreateFromPNG("image/bg".$bgurl.".png");
//Generate the random string
$chars =
array("a","A","b","B","c","C","d","D","e","E","f","F","g",
"G","h","H","i","I","j","J","k",
"K","l","L","m","M","n","N","o","O","p","P","q","Q",
"r","R","s","S","t","T","u","U","v",
"V","w","W","x","X","y","Y","z","Z","1","2","3","4",
"5","6","7","8","9");
$length = 8;
$textstr = "";
for ($i=0; $i<$length; $i++) {
$textstr .= $chars[rand(0, count($chars)-1)];
}
//Create random size, angle, and dark color
$size = rand(12, 16);
$angle = rand(-5, 5);
$color = ImageColorAllocate($im, rand(0, 100), rand(0, 100),
rand(0, 100));
//Determine text size, and use dimensions to generate x & y coordinates
$textsize = imagettfbbox($size, $angle, $font, $textstr);
$twidth = abs($textsize[2]-$textsize[0]);
$theight = abs($textsize[5]-$textsize[3]);
$x = (imagesx($im)/2)-($twidth/2)+(rand(-20, 20));
$y = (imagesy($im))-($theight/2);
//Add text to image
ImageTTFText($im, $size, $angle, $x, $y, $color, $font,
$textstr);
//Output PNG Image
header("Content-Type: image/png");
ImagePNG($im);
//Destroy the image to free memory
imagedestroy($im);
//Insert reference into database, and delete any old ones
$con=mysqli_connect("localhost", "root", "") or die(mysqli_error());
mysqli_select_db($con,"security");
//Create reference
mysqli_query($con,"INSERT INTO security_image (insertdate,referenceid, hiddentext)VALUES (now(), '".$referenceid."', '".$textstr."')");
//Delete references older than 1 day
mysqli_query($con,"DELETE FROM security_image
WHERE insertdate < date_sub(now(), interval 10 minute)");
//End Output
exit;
?>

