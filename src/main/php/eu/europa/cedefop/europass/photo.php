<?php
   /*
	* Copyright European Union 2002-2010
	*
	*
	* Licensed under the EUPL, Version 1.1 or � as soon they 
	* will be approved by the European Commission - subsequent  
	* versions of the EUPL (the "Licence"); 
	* You may not use this work except in compliance with the 
	* Licence. 
	* You may obtain a copy of the Licence at: 
	*
	* http://ec.europa.eu/idabc/eupl.html
	*
	*  
	* Unless required by applicable law or agreed to in 
	* writing, software distributed under the Licence is 
	* distributed on an "AS IS" basis, 
	* WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either 
	* express or implied. 
	* See the Licence for the specific language governing 
	* permissions and limitations under the Licence. 
	*
	*/
# Get the image string from the xml and decode it.
$img_tmp = base64_decode($photo);

#Create a random number to use it for the name of the image.
$ses = rand();

#Create the image file from the string based on the image type.
if ($photo_type == 'image/jpeg' || $photo_type == 'image/jpg') {
    $file = fopen($upload_path.$ses.'-cv.jpg','wb');
    fwrite($file,$img_tmp);
	fclose($file);
	$image = $upload_path.$ses.'-cv.jpg';
	$img = @imagecreatefromjpeg($image);
} else if ($photo_type == 'image/gif') {
    $file = fopen($upload_path.$ses.'-cv.gif','wb');
    fwrite($file,$img_tmp);
	fclose($file);
	$image = $upload_path.$ses.'-cv.gif';
	$img = @imagecreatefrompng($image);
} else if ($photo_type == 'image/png') {
    $file = fopen($upload_path.$ses.'-cv.png','wb');
    fwrite($file,$img_tmp);
	fclose($file);
	$image = $upload_path.$ses.'-cv.png';
	$img = @imagecreatefromgif($image);
}

#Resize the image to fit 113x151.
list($width,$height)=getimagesize($image);
$nwidth=113;
$nheight=151;
$tmp=imagecreatetruecolor($nwidth,$nheight);
imagecopyresampled($tmp,$img,0,0,0,0,$nwidth,$nheight,$width,$height);
imagejpeg($tmp,$image,100);

#Display the image
echo '<img src="'.$image.'"/><br/>';

#Destroy the tmp file.
imagedestroy($tmp);
?>