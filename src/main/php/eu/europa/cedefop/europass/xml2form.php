<?php
   /*
	* Copyright European Union 2002-2010
	*
	*
	* Licensed under the EUPL, Version 1.1 or – as soon they 
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
/* xml2form.php
 * This file is for parsing the XML file and uploading the data in a form.
 *  */
/* ini_set() is used to include in the search path list the directory that we upload the XML or PDF file.
 */
ini_set('include_path', $upload_path);
$xfile = $xml;


/* @var DOMDocument
 * Load the XML File in a DOM Document.
 * */
$doc = new DOMDocument();
$doc->load($xfile);

#Create the page header
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Strict//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>CV Europass Upload</title>
<center><img src="./images/cv_top_banner1.jpg" alt="Europass CV" /></center><br/>
</head>
<body style="background-image: url(./images/bg_win5.jpg); background-repeat: repeat-x; "><font face="Arial Narrow">';
echo '<form>';
# Get from the XML all the elements with tag name 'identification' and load them in a list.
$identifications = $doc->getElementsByTagName("identification");

/* For each on of the list elements get the various elements included in the identification entity
 * and load them in the coresponding variables. */
foreach( $identifications as $identification )
{
	if ($identification->getElementsByTagName("firstname"))
	{
		$firstname    = $identification->getElementsByTagName("firstname")->item(0)->nodeValue;
	} else {$firstname = NULL;}
	if ($identification->getElementsByTagName("lastname")) {
		$lastname     = $identification->getElementsByTagName("lastname")->item(0)->nodeValue;
	} else {$lastname = NULL;}
	if ($identification->getElementsByTagName("addressLine")) {
		$addressLine  = $identification->getElementsByTagName("addressLine")->item(0)->nodeValue;
	} else {$addressLine = NULL;}
	if ($identification->getElementsByTagName("municipality")) {
		$municipality = $identification->getElementsByTagName("municipality")->item(0)->nodeValue;
	} else {$municipality = NULL;}
	if ($identification->getElementsByTagName("postalCode")) {
		$postalCode   = $identification->getElementsByTagName("postalCode")->item(0)->nodeValue;
	} else {$postalCode = NULL;}
	if ($identification->getElementsByTagName("country")) {
		$countries    = $identification->getElementsByTagName("country");
		foreach ($countries as $country)
		{
			if ($identification->getElementsByTagName("code")->item(0)) {
				$code  = $country->getElementsByTagName("code")->item(0)->nodeValue;
			} else {$code = NULL;}
			if ($identification->getElementsByTagName("label")->item(0)) {
				$label = $country->getElementsByTagName("label")->item(0)->nodeValue;
			} else {$label = NULL;}
		}
	} else {
		$code = NULL;
		$label = NULL;
	}
	if ($identification->getElementsByTagName("telephone")) {
		$telephone  = $identification->getElementsByTagName("telephone")->item(0)->nodeValue;
	} else {$telephone = NULL;}
	if ($identification->getElementsByTagName("fax")) {
		$fax        = $identification->getElementsByTagName("fax")->item(0)->nodeValue;
	} else {$fax = NULL;}
	if ($identification->getElementsByTagName("mobile")) {
		$mobile     = $identification->getElementsByTagName("mobile")->item(0)->nodeValue;
	} else {$mobile = NULL;}
	if ($identification->getElementsByTagName("email")) {
		$email      = $identification->getElementsByTagName("email")->item(0)->nodeValue;
	} else {$email = NULL;}
	if ($identification->getElementsByTagName("gender")) {
		$gender     = $identification->getElementsByTagName("gender")->item(0)->nodeValue;
	} else {$gender = NULL;}
	if ($identification->getElementsByTagName("birthdate")->item(0)) {
		$birthdate  = $identification->getElementsByTagName("birthdate")->item(0)->nodeValue;
	} else {$birthdate = NULL;}
	if ($identification->getElementsByTagName("photo")->item(0)) {
		$photo      = $identification->getElementsByTagName("photo")->item(0)->nodeValue;
		$photo_type = $identification->getElementsByTagName("photo")->item(0)->getAttribute("type");
	} else {$photo = NULL; $photo_type = NULL;}
	
	#If the data exist, load the first step data in the form
	echo '<br/><h2>Identification</h2><br/><img src="./images/bg_win.jpg" /><br/>';
	if ($photo != NULL) {
		$img = include('photo.php');
	}
	
	if ($firstname != NULL)	{echo '<label style="font-weight: bold; ">First Name: </label>'.$firstname.' ';}
	if ($lastname != NULL)	{echo '<label style="font-weight: bold; ">Last Name: </label>'.$lastname.'<br/><br/>';}
	if ($birthdate != NULL)	{echo '<label style="font-weight: bold; ">Date of Birth: </label>'.$birthdate.'<br/><br/>';}
	echo '<b>Address</b><br/>';
	if ($addressLine != NULL)	{echo '<label style="font-weight: bold; ">Street number / Street: </label>'.$addressLine.'<br/>';}
	if ($municipality != NULL)	{echo '<label style="font-weight: bold; ">City: </label>'.$municipality.'<br/>';}
	if ($postalCode != NULL)	{echo '<label style="font-weight: bold; ">Postal code: </label>'.$postalCode.'<br/>';}
	if ($label != NULL)			{echo '<label style="font-weight: bold; ">Country: </label>'.$label.'<br/>';}
	echo '<br/>';
	if ($telephone != NULL)		{echo '<label style="font-weight: bold; ">Telephone(s): </label>'.$telephone.'<br/>';}
	if ($mobile != NULL)		{echo '<label style="font-weight: bold; ">Mobile: </label>'.$mobile.'<br/>';}
	if ($fax != NULL)			{echo '<label style="font-weight: bold; ">Fax: </label>'.$fax.'<br/>';}
	if ($email != NULL)			{echo '<label style="font-weight: bold; ">E-mail(s): </label>'.$email.'<br/>';}
	
	$nationalities = $identification->getElementsByTagName("nationality");
	foreach ($nationalities as $nationality)
	{
		if ($nationality->getElementsByTagName("code")->item(0))
		{
			$ncode  = $nationality->getElementsByTagName("code")->item(0)->nodeValue;
		} else
		{
			$ncode = NULL;
		}
		if ($nationality->getElementsByTagName("label")->item(0))
		{
			$nlabel = $nationality->getElementsByTagName("label")->item(0)->nodeValue;
		} else
		{
			$nlabel = NULL;
		}
		#Load the Nationality(s) in the form
		if ($nlabel != NULL)	{echo "<b>Nationality: </b>".$nlabel."<br/>";}
	}
}
	
	if ($gender != NULL) {
		if      ($gender == "M") {echo '<label style="font-weight: bold; ">Gender: </label>Male<br/>';}
		else if ($gender == "F") {echo '<label style="font-weight: bold; ">Gender: </label>Female<br/>';}
		else 					 {echo '<label style="font-weight: bold; ">Gender: </label>Not Available<br/>';}
	}


# Load the data of the second step, included in the <application> tag.
$applications = $doc->getElementsByTagName("application");
foreach( $applications as $application )
{
	if ($application->getElementsByTagName("code")->item(0)) {
		$appcode  = $application->getElementsByTagName("code")->item(0)->nodeValue;
	} else {$appcode = NULL;}
	if ($application->getElementsByTagName("label")->item(0)) {
		$applabel = $application->getElementsByTagName("label")->item(0)->nodeValue;
	} else {$applabel = NULL;}
	
	#Load the Application in the form
	if ($applabel != NULL) {
		echo '<br/><h2>Desired employment/Occupational field</h2><br/><img src="./images/bg_win.jpg" /><br/>';
		echo '<label for"application" style="font-weight: bold; display:block;">Desired employment/Occupational field</label></br><textarea id="additional" rows="5" cols="60" readonly="true">'.$applabel.'</textarea><br/>';
		
	}
}


#Load the data of the third step, included in the <workexperiencelist> tag.
$workexperiencelist = $doc->getElementsByTagName("workexperience");
if ($workexperiencelist->length > 0)
	echo '<br/><h2>Work Experience</h2><br/><img src="./images/bg_win.jpg" /><br/>';
	foreach ($workexperiencelist as $workexperience)
	{
		$froms = $workexperience->getElementsByTagName("from");
		foreach ($froms as $from)
		{
			if($from->getElementsByTagName("year")->item(0)) {
				$fyear = $from->getElementsByTagName("year")->item(0)->nodeValue;
			} else {$fyear = NULL;}
			if($from->getElementsByTagName("month")->item(0)) {
				$fmonth = trim($from->getElementsByTagName("month")->item(0)->nodeValue,'-');
			} else {$fmonth = NULL;}
			if($from->getElementsByTagName("day")->item(0)) {
				$fday = trim($from->getElementsByTagName("day")->item(0)->nodeValue,'-');
			} else {$fday = NULL;}
		}
		$tos = $workexperience->getElementsByTagName("to");
		foreach ($tos as $to)
		{
			if($to->getElementsByTagName("year")->item(0)) {
				$tyear = $to->getElementsByTagName("year")->item(0)->nodeValue;
			} else {$tyear = NULL;}
			if($to->getElementsByTagName("month")->item(0)) {
				$tmonth = trim($to->getElementsByTagName("month")->item(0)->nodeValue,'-');
			} else {$tmonth = NULL;}
			if($to->getElementsByTagName("day")->item(0)) {
				$tday = trim($to->getElementsByTagName("day")->item(0)->nodeValue,'-');
			} else {$tday = NULL;}
		}
		$positions = $workexperience->getElementsByTagName("position");
		foreach ($positions as $position)
		{
			if($position->getElementsByTagName("code")->item(0)) {
				$pcode = $position->getElementsByTagName("code")->item(0)->nodeValue;
			} else {$pcode = NULL;}
			if($position->getElementsByTagName("label")->item(0)) {
				$plabel = $position->getElementsByTagName("label")->item(0)->nodeValue;
			} else {$plabel = NULL;}
		}
		if ($workexperience->getElementsByTagName("activities")->item(0)) {
			$wactivities = $workexperience->getElementsByTagName("activities")->item(0)->nodeValue;
		} else {$wactivities = NULL;}
		if ($workexperience->getElementsByTagName("name")->item(0)) {
			$wname = $workexperience->getElementsByTagName("name")->item(0)->nodeValue;
		} else {$wname = NULL;}
		if ($workexperience->getElementsByTagName("addressLine")->item(0)) {
			$waddress = $workexperience->getElementsByTagName("addressLine")->item(0)->nodeValue;
		} else {$waddress = NULL;}
		if ($workexperience->getElementsByTagName("municipality")->item(0)) {
			$wcity = $workexperience->getElementsByTagName("municipality")->item(0)->nodeValue;
		} else {$wcity = NULL;}
		if ($workexperience->getElementsByTagName("postalCode")->item(0)) {
			$wpcode = $workexperience->getElementsByTagName("postalCode")->item(0)->nodeValue;
		} else {$wpcode = NULL;}
		$countries = $workexperience->getElementsByTagName("country");
		foreach ($countries as $country)
		{
			if($country->getElementsByTagName("code")->item(0)) {
				$weccode = $country->getElementsByTagName("code")->item(0)->nodeValue;
			} else {$weccode = NULL;}
			if($country->getElementsByTagName("label")->item(0)) {
				$weclabel = $country->getElementsByTagName("label")->item(0)->nodeValue;
			} else {$weclabel = NULL;}
		}
		$sectors = $workexperience->getElementsByTagName("sector");
		foreach ($sectors as $sector)
		{
			if($sector->getElementsByTagName("code")->item(0)) {
				$weseccode = $sector->getElementsByTagName("code")->item(0)->nodeValue;
			} else {$weseccode = NULL;}
			if($sector->getElementsByTagName("label")->item(0)) {
				$weseccodelabel = $sector->getElementsByTagName("label")->item(0)->nodeValue;
			} else {$weseccodelabel = NULL;}
		}
	
	#Load the Work Experience(s) in the form
	echo '<hr size="1">';
	if ($fday != NULL || $fmonth!= NULL || $fyear != NULL ) { echo '<label style="font-weight: bold; ">From: </label>'.$fday.'/'.$fmonth.'/'.$fyear.' ';}
	if ($tday != NULL || $tmonth!= NULL || $tyear != NULL ) { echo '<label style="font-weight: bold; ">To: </label>'.$tday.'/'.$tmonth.'/'.$tyear;}
	echo '<br/>';
	if ($plabel != NULL)			{echo '<label style="font-weight: bold; ">Occupation or position held: </label>'.$plabel.'<br/>';}
	if ($wactivities != NULL)		{echo '<label style="font-weight: bold; ">Main activities and responsibilities: </label>'.$wactivities.'<br/><br/>';}
	echo '<label style="font-weight: bold; ">Name and address of employer</label><br/>';
	if ($wname != NULL)				{echo '<label style="font-weight: bold; ">Name: </label>'.$wname.'<br/>';}
	if ($waddress != NULL)			{echo '<label style="font-weight: bold; ">Street number / Street: </label>'.$waddress.'<br/>';}
	if ($wcity != NULL)				{echo '<label style="font-weight: bold; ">City: </label>'.$wcity.'<br/>';}
	if ($wpcode != NULL)			{echo '<label style="font-weight: bold; ">Postal code: </label>'.$wpcode.'<br/>';}
	if ($weclabel != NULL)			{echo '<label style="font-weight: bold; ">Country: </label>'.$weclabel.'<br/><br/>';}
	if ($weseccodelabel != NULL)	{echo '<label style="font-weight: bold; ">Type of business or sector: </label>'.$weseccodelabel.'<br/>';}
	echo "<br/>";
		
	}

#Load the data of the third step, included in the <education> tag.
$educationlist = $doc->getElementsByTagName("education");
if ($educationlist->length > 0)
	echo '<br/><h2>Education and training</h2><br/><img src="./images/bg_win.jpg" /><br/>';
	foreach ($educationlist as $education)
	{
		$efroms = $education->getElementsByTagName("from");
		foreach ($efroms as $efrom)
		{
			if($efrom->getElementsByTagName("year")->item(0)) {
				$efyear = $efrom->getElementsByTagName("year")->item(0)->nodeValue;
			} else {$efyear = NULL;}
			if($efrom->getElementsByTagName("month")->item(0)) {
				$efmonth = trim($efrom->getElementsByTagName("month")->item(0)->nodeValue,'-');
			} else {$efmonth = NULL;}
			if($efrom->getElementsByTagName("day")->item(0)) {
				$efday = trim($efrom->getElementsByTagName("day")->item(0)->nodeValue,'-');
			} else {$efday = NULL;}
		}
		$etos = $education->getElementsByTagName("to");
		foreach ($etos as $eto)
		{
			if($eto->getElementsByTagName("year")->item(0)) {
				$etyear = $eto->getElementsByTagName("year")->item(0)->nodeValue;
			} else {$etyear = NULL;}
			if($eto->getElementsByTagName("month")->item(0)) {
				$etmonth = trim($eto->getElementsByTagName("month")->item(0)->nodeValue,'-');
			} else {$etmonth = NULL;}
			if($eto->getElementsByTagName("day")->item(0)) {
				$etday = trim($eto->getElementsByTagName("day")->item(0)->nodeValue,'-');
			} else {$etday = NULL;}
		}
		if ($education->getElementsByTagName("title")->item(0)) {
			$title = $education->getElementsByTagName("title")->item(0)->nodeValue;
		} else {$title = NULL;}
		if ($education->getElementsByTagName("skills")->item(0)) {
			$eskills = $education->getElementsByTagName("skills")->item(0)->nodeValue;
		} else {$eskills = NULL;}
		
		if ($education->getElementsByTagName("name")->item(0)) {
			$ename = $education->getElementsByTagName("name")->item(0)->nodeValue;
		} else {$ename = NULL;}
		if ($education->getElementsByTagName("addressLine")->item(0)) {
			$eaddress = $education->getElementsByTagName("addressLine")->item(0)->nodeValue;
		} else {$eaddress = NULL;}
		if ($education->getElementsByTagName("municipality")->item(0)) {
			$ecity = $education->getElementsByTagName("municipality")->item(0)->nodeValue;
		} else {$ecity = NULL;}
		if ($education->getElementsByTagName("postalCode")->item(0)) {
			$epcode = $education->getElementsByTagName("postalCode")->item(0)->nodeValue;
		} else {$epcode = NULL;}
		
		$ecountries = $education->getElementsByTagName("country");
		foreach ($ecountries as $ecountry)
		{
			if($ecountry->getElementsByTagName("code")->item(0)) {
				$educcode = $ecountry->getElementsByTagName("code")->item(0)->nodeValue;
			} else {$educcode = NULL;}
			if($ecountry->getElementsByTagName("label")->item(0)) {
				$educlabel = $ecountry->getElementsByTagName("label")->item(0)->nodeValue;
			} else {$educlabel = NULL;}
		}
		if ($education->getElementsByTagName("type")->item(0)) {
			$orgtype = $education->getElementsByTagName("type")->item(0)->nodeValue;
		}
		
		$levels = $education->getElementsByTagName("level");
		foreach ($levels as $level)
		{
			if($level->getElementsByTagName("code")->item(0)) {
				$edulcode = $level->getElementsByTagName("code")->item(0)->nodeValue;
			} else {$edulcode = NULL;}
			if($level->getElementsByTagName("label")->item(0)) {
				$edullabel = $level->getElementsByTagName("label")->item(0)->nodeValue;
			} else {$edullabel = NULL;}
		}
		
		$edufields = $education->getElementsByTagName("educationalfield");
		foreach ($edufields as $edufield)
		{
			if($edufield->getElementsByTagName("code")->item(0)) {
				$edufcode = $edufield->getElementsByTagName("code")->item(0)->nodeValue;
			} else {$edufcode = NULL;}
			if($edufield->getElementsByTagName("label")->item(0)) {
				$eduflabel = $edufield->getElementsByTagName("label")->item(0)->nodeValue;
			} else {$eduflabel = NULL;}
		}
		
		#Load the Education(s) in the form
		echo '<hr size="1">';
		if ($efday != NULL || $efmonth!= NULL || $efyear != NULL ) { echo "<b>From:</b> ".$efday."/".$efmonth."/".$efyear." ";}
		if ($etday != NULL || $etmonth!= NULL || $etyear != NULL ) { echo "<b>To:</b> ".$etday."/".$etmonth."/".$etyear;}
		echo "<br/>";
		if ($title != NULL)				{echo "<b>Title of qualification awarded: </b>".$title."<br/>";}
		if ($eskills != NULL)			{echo "<b>Principal subjects / occupational skills covered: </b>".$eskills."<br/>";}
		echo "<b>Organisation providing education and training</b><br/>";
		if ($ename != NULL)				{echo "<b>Name: </b>".$ename."<br/>";}
		if ($orgtype != NULL)			{echo "<b>Type: </b>".$orgtype."<br/>";}
		if ($eaddress != NULL)			{echo "<b>Street number / Street: </b>".$eaddress."<br/>";}
		if ($ecity != NULL)				{echo "<b>City: </b>".$ecity."<br/>";}
		if ($epcode != NULL)			{echo "<b>Postal code: </b>".$epcode."<br/>";}
		if ($educlabel != NULL)			{echo "<b>Country: </b>".$educlabel."<br/><br/>";}
		if ($edullabel != NULL)			{echo "<b>Level in national or international classification: </b>".$edullabel."<br/><br/>";}
		if ($eduflabel != NULL)			{echo "<b>Educational field: </b>".$eduflabel."<br/>";}
		echo "<br/>";
	}

#Load the data of the third step, included in the <languagelist> tag.
$languagelists = $doc->getElementsByTagName("languagelist");

echo '<br/><h2>Language(s)</h2><br/><img src="./images/bg_win.jpg" /><br/>';
foreach ($languagelists as $languagelist)
{
	$languages = $languagelist->getElementsByTagName("language");
	$num = 0;
	foreach ($languages as $language)
	{			
		#Check if the language is the mother language or a foreign language.
		switch ($language->getAttribute("xsi:type"))
		{
			case "europass:mother" :
				if ($language->getElementsByTagName("code")->item(0)) {
				$mlcode  = $language->getElementsByTagName("code")->item(0)->nodeValue;
				} else {$mlcode = NULL;}
				$mllabel = $language->getElementsByTagName("label")->item(0)->nodeValue;
				
				#Load the Mother Language in the form
				if ($mllabel != NULL) {
					echo '<label for"ml" style="font-weight: bold; display:block;">Mother tongue(s)</label></br><textarea id="ml" rows="3" cols="40" readonly="true">'.$mllabel.'</textarea><br/>';
					echo '<br/><h3>Other language(s) - Self-assessment</h3><br/>';
				}
				break;
			case "europass:foreign" :
				$flcode             = $language->getElementsByTagName("code")->item($num)->nodeValue;
				$fllabel            = $language->getElementsByTagName("label")->item($num)->nodeValue;
				$listening          = $language->getElementsByTagName("listening")->item($num)->nodeValue;
				$reading            = $language->getElementsByTagName("reading")->item($num)->nodeValue;
				$spokeninteraction  = $language->getElementsByTagName("spokeninteraction")->item($num)->nodeValue;
				$spokenproduction   = $language->getElementsByTagName("spokenproduction")->item($num)->nodeValue;
				$writing            = $language->getElementsByTagName("writing")->item($num)->nodeValue;
				#Load the Other Language(s) in the form
				echo '<hr size="1">';
				if ($fllabel != NULL)			{echo "<b>Language: </b>".$fllabel."<br/><br/>";}
				echo '<b>Understanding</b><br/>';
				if ($listening != NULL)			{echo "<b>Listening: </b>".$listening."<br/>";}
				if ($reading != NULL)			{echo "<b>Reading: </b>".$reading."<br/><br/>";}
				echo '<b>Speaking</b><br/>';
				if ($spokeninteraction != NULL)	{echo "<b>Spoken interaction: </b>".$spokeninteraction."<br/>";}
				if ($spokenproduction != NULL)	{echo "<b>Spoken production: </b>".$spokenproduction."<br/><br/>";}
				if ($writing != NULL)			{echo "<b>Writing: </b>".$writing."<br/>";}
				echo '<br/>';
				break;
		}	
		
	}
			
}


#Load the data of the third step, included in the <skilllist> tag.
$skilllists = $doc->getElementsByTagName("skilllist");
$k = 0;
foreach( $skilllists as $skilllist )
{
	$skillitems = $skilllist->getElementsByTagName("skill");
	foreach($skillitems as $skillitem)
	{
		$social         = $skillitems->item(0)->nodeValue;
		$organisational = $skillitems->item(1)->nodeValue;
		$technical      = $skillitems->item(2)->nodeValue;
		$computer       = $skillitems->item(3)->nodeValue;
		$artistic       = $skillitems->item(4)->nodeValue;
		$other          = $skillitems->item(5)->nodeValue;
	}
	#Load the Personal Skills in the form
	echo '<br/><h2>Personal skills and competences </h2><br/><img src="./images/bg_win.jpg" /><br/>';
	if ($social != NULL)			{echo '<label for"social" style="font-weight: bold; display:block;">Social skills and competences</label></br><textarea id="social" rows="5" cols="60" readonly="true">'.$social."</textarea><br/><br/>";}			
	if ($organisational != NULL)	{echo '<label for"organ" style="font-weight: bold; display:block;">Organizational skills and competences</label></br><textarea id="organ" rows="5" cols="60" readonly="true">'.$organisational."</textarea><br/><br/>";}
	if ($technical != NULL)			{echo '<label for"tech" style="font-weight: bold; display:block;">Technical skills and competences</label></br><textarea id="tech" rows="5" cols="60" readonly="true">'.$technical."</textarea><br/><br/>";}
	if ($computer != NULL)			{echo '<label for"computer" style="font-weight: bold; display:block;">Computer skills and competences</label></br><textarea id="computer" rows="5" cols="60" readonly="true">'.$computer."</textarea><br/><br/>";}
	if ($artistic != NULL)			{echo '<label for"artistic" style="font-weight: bold; display:block;">Artistic skills and competences</label></br><textarea id="artistic" rows="5" cols="60" readonly="true">'.$artistic."</textarea><br/><br/>";}
	if ($other != NULL)				{echo '<label for"other" style="font-weight: bold; display:block;">Other skills and competences</label></br><textarea id="other" rows="5" cols="60" readonly="true">'.$other."</textarea><br/>";}
	
}
#Load the data of the third step, included in the <drivinglicence> tag.
$drivinglist = $doc->getElementsByTagName("drivinglicence");
$drivingcnt = $drivinglist->length;
	echo '<br/><h3>Driving licence(s)</h3><br/>';
	if ($drivingcnt == 0) {echo 'No Driving License.<br/><br/>';}
if ($drivingcnt > 0)
{
	for ($idx = 0; $idx < $drivingcnt; $idx++) {
		$driving_licence = $drivinglist->item($idx)->nodeValue;
		switch ($driving_licence) {
			#Load the Driving Licence(s) in the form
			case 'A1' :
				echo '<img alt="'.$driving_licence.'" src="./images/driving/A1.jpg" height="30" width="90"/><br/>';
				break;
			case 'A':
				echo  '<img alt="'.$driving_licence.'" src="./images/driving/A.jpg" height="30" width="90"/><br/>';
				break;
			case 'B':
				echo  '<img alt="'.$driving_licence.'" src="./images/driving/B.jpg" height="30" width="90"/><br/>';
				break;
			case 'B1':
				echo  '<img alt="'.$driving_licence.'" src="./images/driving/B1.jpg" height="30" width="90"/><br/>';
				break;
			case 'BE':
				echo  '<img alt="'.$driving_licence.'" src="./images/driving/BE.jpg" height="30" width="90"/><br/>';
				break;
			case 'C':
				echo  '<img alt="'.$driving_licence.'" src="./images/driving/C.jpg" height="30" width="90"/><br/>';
				break;
			case 'C1':
				echo  '<img alt="'.$driving_licence.'" src="./images/driving/C1.jpg" height="30" width="90"/><br/>';
				break;
			case 'CE':
				echo  '<img alt="'.$driving_licence.'" src="./images/driving/CE.jpg" height="30" width="90"/><br/>';
				break;
			case 'C1E':
				echo  '<img alt="'.$driving_licence.'" src="./images/driving/C1E.jpg" height="30" width="90"/><br/>';
				break;
			case 'D':
				echo  '<img alt="'.$driving_licence.'" src="./images/driving/D.jpg" height="30" width="90"/><br/>';
				break;
			case 'D1':
				echo  '<img alt="'.$driving_licence.'" src="./images/driving/D1.jpg" height="30" width="90"/><br/>';
				break;
			case 'DE':
				echo  '<img alt="'.$driving_licence.'" src="./images/driving/DE.jpg" height="30" width="90"/><br/>';
				break;
			case 'D1E':
				echo  '<img alt="'.$driving_licence.'" src="./images/driving/D1E.jpg" height="30" width="90"/><br/>';
				break;
		}	
	}
}

#Load the data of the third step, included in the <misclist> tag.
$misclists = $doc->getElementsByTagName("misclist");
foreach( $misclists as $misclist )
{
	$miscitems = $misclist->getElementsByTagName("misc");
	foreach($miscitems as $miscitem)
	{
		$additional = $miscitems->item(0)->nodeValue;
		$annexes    = $miscitems->item(1)->nodeValue;
	}
#Load the Additional Information and Annexes in the form	
echo '<br/><h2>Additional information and annexes </h2><br/><img src="./images/bg_win.jpg" /><br/>';
	if ($additional != NULL)	{echo '<label for"additional" style="font-weight: bold; display:block;">Additional information</label></br><textarea id="additional" rows="5" cols="60" readonly="true">'.$additional.'</textarea><br/><br/>';}
	if ($annexes != NULL)	{echo '<label for"snnexes" style="font-weight: bold; display:block;">Annexes</label></br><textarea id="annexes" rows="5" cols="60" readonly="true">'.$annexes."</textarea>";}
	
}

#Delete the uploaded file
unlink($xml);
echo '<hr size ="2"/>';
echo '<center><a href="index.html">Go Back</a></center>';
echo '</font></form></body></html>';
?>