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
/* upload.php
 * The file used to upload the XML or PDF+XML file.
 */


namespace eu\europa\cedefop\europass;
include 'RestServiceHandler.php';

// Get RestServiceHandler instance
$rsHandlerInstance = RestServiceHandler::getInstance();

/* @var $target_path
 * Tempory Directory to upload the file.
 * */
$upload_path = "tmp/";
$target_path = $upload_path.basename( $_FILES['uploadedxml']['name']);
$target_output_path = $upload_path.basename( 'xmlOutput.xml');

/* @var $maxfilesize
 * Maximum file size.
 *  */
$maxfilesize=1024000;

#Check if the file exceeds the specified file size.
if ($_FILES['uploadedxml']['size'] > $maxfilesize) {
	echo '<center><img src="./images/cv_top_banner1.jpg" alt="Europass CV" /></center><br/><HR size="2"/><br/>';
	echo 'Your file is too large.<br/><center><a href="index.html">Go Back</a></center>';
	unlink($_FILES['uploadedxml']['tmp_name']); #Delete the temp file
}

#Check if the file is a XML or PDF+XML file.

if ($_FILES['uploadedxml']['type'] == NULL) {
	echo '<center><img src="./images/cv_top_banner1.jpg" alt="Europass CV" /></center><br/><HR size="2"/><br/>';
	echo 'Please select a file to upload.<br/>';
	echo '<a href="index.html">Go Back</a>';
} else if ($_FILES['uploadedxml']['type'] !="text/xml" && $_FILES['uploadedxml']['type'] !="application/pdf") {
	echo '<center><img src="./images/cv_top_banner1.jpg" alt="Europass CV" /></center><br/><HR size="2"/><br/>';
	echo "You are trying to upload a file of ".$_FILES['uploadedxml']['type']." type.<br/>";
	echo "You can upload only XML or PDF+XML files.<br/>";
	echo '<a href="index.html">Go Back</a>';
}

#If everything is ok we try to upload it and start the parsing.
else {
	if(move_uploaded_file($_FILES['uploadedxml']['tmp_name'], $target_path)) {
        #Check if the file is PDF/ XML
		if ($_FILES['uploadedxml']['type'] == "application/pdf") {
            $rsHandlerInstance->xmlExtractFromPDF($target_path);
            $xml = $target_output_path;
			unlink($target_path);
		} else {
			$xml = $target_path;
		}
        #Check if the user wants to upload the file to the database or to a form.
		if      ($_POST['upload'] == 'sql') 	{include('db_connect.php');} #User wants to upload the file in the db
		else if ($_POST['upload'] == 'form')	{include('xml2form.php');} #User wants to upload the file in a form
	} else {
		echo '<center><img src="./images/cv_top_banner1.jpg" alt="Europass CV" />';
		echo 'Sorry, there was a problem uploading your file.<br />';
		echo '<a href="index.html">Go Back</a>';
		unlink($_FILES['uploadedxml']['tmp_name']); #Delete the temp file
	}
}
?>