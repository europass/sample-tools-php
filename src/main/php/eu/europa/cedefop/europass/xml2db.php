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
/* xml2db.php
 * This file is for parsing the XML file and inserting the data in the MY SQL database.
 *  */
/* ini_set() is used to include in the search path list the directory that we upload the XML or PDF file.
 */
ini_set('include_path', $upload_path);

/* @var DOMDocument
 * Load the XML File in a DOM Document.
 * */
$doc = new DOMDocument();
$doc->load($xml);

/* Load the data of the first step, included in the <identification> tag.
 * The data are loaded in mob_xml table.*/

# Get from the XML all the elements with tag name 'identification' and load them in a list.
$identifications = $doc->getElementsByTagName("identification");
foreach( $identifications as $identification )
{
/* For each on of the list elements get the various elements included in the identification entity
 * and load them in the coresponding variables. */
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

	#Insert the first set of data in mob_xml table
    mysqli_query($link,"INSERT INTO mob_xml (ID,  FNAME, LNAME, ADDRESS, MUNIC, POSTAL_CODE, CODE_COUNTRY, COUNTRY, PHONE,
									  FAX, MOBILE, EMAIL, GENDER, BIRTHDATE, PHOTO_TYPE, PHOTO)
							  VALUES (NULL,'$firstname','$lastname','$addressLine','$municipality','$postalCode','$code','$label',
									  '$telephone','$fax','$mobile','$email','$gender','$birthdate','$photo_type','$photo')")
    or die('Could not insert data in Master table!!!!<br/><center><a href="index.html">Go Back</a></center>'.mysqli_error($link));
	
	/* Retrive the generated id for the insert.
	 * We will use it later to update the master table along with the detail ones with the rest of the data.
	 */
	$xmlid = mysqli_insert_id($link);

	# Load the different nationalities in the coresponding variables
	$nationalities = $identification->getElementsByTagName("nationality");
	/* For each on of the list elements get the various elements included in the nationality entity
 	* and load them in the coresponding variables. */
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
		#Insert the data in the mob_nationality table
        mysqli_query($link,"INSERT INTO mob_nationality (ID, XML_ID, CODE, NATIONALITY)
										  VALUES (NULL,'$xmlid','$ncode','$nlabel')")
        or die('Could not insert data in Nationality Table<br/><center><a href="index.html">Go Back</a></center>'.mysqli_error($link));

	}
}


# Get from the XML all the elements with tag name 'application' and load them in a list.
$applications = $doc->getElementsByTagName("application");
/* For each on of the list elements get the various elements included in the application entity
* and load them in the coresponding variables. */
foreach( $applications as $application )
{
	if ($application->getElementsByTagName("code")->item(0)) {
		$appcode  = $application->getElementsByTagName("code")->item(0)->nodeValue;
	} else {$appcode = NULL;}
	if ($application->getElementsByTagName("label")->item(0)) {
		$applabel = $application->getElementsByTagName("label")->item(0)->nodeValue;
	} else {$applabel = NULL;}
	#Update the data in the mob_xml table
    mysqli_query($link,"UPDATE mob_xml
					SET CODE_APPLICATION = '$appcode',
						APPLICATION		 = '$applabel'
				  WHERE ID = '$xmlid'") or die('Could not update Master Table with Application data!!!<br/><center><a href="index.html">Go Back</a></center>'.mysqli_error($link));
}


# Get from the XML all the elements with tag name 'workexperience' and load them in a list.
$workexperiencelist = $doc->getElementsByTagName("workexperience");
if ($workexperiencelist->length > 0)
	/* For each on of the list elements get the various elements included in the workexperience entity
 	* and load them in the coresponding variables. */
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
		#Insert the data in the mob_work_experience table
        mysqli_query($link,"INSERT INTO mob_work_experience (ID, XML_ID, DAY_FROM, MONTH_FROM, YEAR_FROM, DAY_TO, MONTH_TO, YEAR_TO,
													  CODE_POSITION, WPOSITION, ACTIVITIES, EMPLOYER_NAME, EMPLOYER_ADDRESS,
													  EMPLOYER_MUNIC, EMPLOYER_ZCODE, CODE_COUNTRY, COUNTRY, CODE_SECTOR, SECTOR)
										  VALUES     (NULL,'$xmlid','$fday','$fmonth','$fyear','$tday','$tmonth','$tyear','$pcode','$plabel','$wactivities',
													  '$wname','$waddress','$wcity','$wpcode','$weccode','$weclabel','$weseccode','$weseccodelabel' )")
        or die('Could not insert data in Work Experience table!!!!<br/><center><a href="index.html">Go Back</a></center>'.mysqli_error($link));
	}

# Get from the XML all the elements with tag name 'education' and load them in a list.
$educationlist = $doc->getElementsByTagName("education");
if ($educationlist->length > 0)
	/* For each on of the list elements get the various elements included in the education entity
 	* and load them in the coresponding variables. */
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
            $title = mysqli_real_escape_string($link, $education->getElementsByTagName("title")->item(0)->nodeValue);
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
		#Insert the data in the mob_education table
        mysqli_query($link,"INSERT INTO mob_education (ID, XML_ID, TITLE, SUBJECT, ORG_NAME, ORG_TYPE, ORG_ADDRESS, ORG_MUNIC,
											    ORG_ZCODE, CODE_COUNTRY, COUNTRY, CODE_LEVEL, EDULEVEL,CODE_EDU_FIELD,EDU_FIELD,
												DAY_FROM, MONTH_FROM, YEAR_FROM, DAY_TO, MONTH_TO, YEAR_TO)
										VALUES (NULL,'$xmlid','$title','$eskills','$ename','$orgtype','$eaddress','$ecity','$epcode',
												'$educcode','$educlabel','$edulcode','$edullabel','$edufcode','$eduflabel',
												'$efday','$efmonth','$efyear','$etday','$etmonth','$etyear')")
        or die('Could not insert data in Education table!!!!<br/><center><a href="index.html">Go Back</a></center>'.mysqli_error($link));
	}

# Get from the XML all the elements with tag name 'languagelist' and load them in a list.
$languagelists = $doc->getElementsByTagName("languagelist");

/* For each on of the list elements get the various elements included in the languagelist entity
* and load them in the coresponding variables. */
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
				#Update the data in the mob_xml table with the mother_language
                mysqli_query($link,"UPDATE mob_xml
					         SET CODE_MOTHER_LANGUAGE = '$mlcode',
						         MOTHER_LANGUAGE	  = '$mllabel'
							 WHERE ID = '$xmlid'") or die('Could not update Master table with Mother Language!!!!<br/>'.mysqli_error($link));
				break;
			case "europass:foreign" :
				$flcode             = $language->getElementsByTagName("code")->item($num)->nodeValue;
				$fllabel            = $language->getElementsByTagName("label")->item($num)->nodeValue;
				$listening          = $language->getElementsByTagName("listening")->item($num)->nodeValue;
				$reading            = $language->getElementsByTagName("reading")->item($num)->nodeValue;
				$spokeninteraction  = $language->getElementsByTagName("spokeninteraction")->item($num)->nodeValue;
				$spokenproduction   = $language->getElementsByTagName("spokenproduction")->item($num)->nodeValue;
				$writing            = $language->getElementsByTagName("writing")->item($num)->nodeValue;
				#Insert the data in the mob_language table
                mysqli_query($link,"INSERT INTO mob_language (ID, XML_ID, CODE_LANGUAGE, OLANGUAGE, LISTENING, READING,
													   SPOKEN_INTERACTION, SPOKEN_PRODUCTION, WRITING)
											   VALUES (NULL,'$xmlid','$flcode','$fllabel','$listening','$reading',
											   		   '$spokeninteraction','$spokenproduction','$writing')") or die('Could not insert data in Language table!!!!<br/><center><a href="index.html">Go Back</a></center>'.mysqli_error($link));
				break;
		}	
		
	}
}


# Get from the XML all the elements with tag name 'skilllist' and load them in a list.
$skilllists = $doc->getElementsByTagName("skilllist");
$k = 0;
/* For each on of the list elements get the various elements included in the skilllist entity
* and load them in the coresponding variables. */
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
	#Update the data in the mob_xml table with the data of the skill section
    mysqli_query($link,"UPDATE mob_xml
				 SET SOCIAL           = '$social',
					 ORGANISATIONAL	  = '$organisational',
					 TECHNICAL		  = '$technical',
					 COMPUTER		  = '$computer',
					 ARTISTIC		  = '$artistic',
					 OTHER			  = '$other'
				 WHERE ID = '$xmlid'")
    or die('Could not update data in Master table with step 6 data!!!!<br/><center><a href="index.html">Go Back</a></center>'.mysqli_error($link));
}

# Get from the XML all the elements with tag name 'drivinglicence' and load them in a list.
$drivinglist = $doc->getElementsByTagName("drivinglicence");
$drivingcnt = $drivinglist->length;

if ($drivingcnt > 0)
{
	for ($idx = 0; $idx < $drivingcnt; $idx++) {
		$driving_licence = $drivinglist->item($idx)->nodeValue;
		#Insert the data in the mob_driving_licence table
        mysqli_query($link,"INSERT INTO mob_driving_licence (ID, XML_ID, DRIVING_SKILL)
											  VALUES (NULL,'$xmlid','$driving_licence')")
        or die('Could not insert data in Driving Skill table!!!!<br/><center><a href="index.html">Go Back</a></center>');
		
	}
}

# Get from the XML all the elements with tag name 'misclist' and load them in a list.
$misclists = $doc->getElementsByTagName("misclist");

/* For each on of the list elements get the various elements included in the misclist entity
* and load them in the coresponding variables. */
foreach( $misclists as $misclist )
{
	$miscitems = $misclist->getElementsByTagName("misc");
	foreach($miscitems as $miscitem)
	{
		$additional = $miscitems->item(0)->nodeValue;
		$annexes    = $miscitems->item(1)->nodeValue;
	}
	#Update the data in the mob_xml table with the data of the misc skill section
    mysqli_query($link,"UPDATE mob_xml
				 SET ADDITIONAL	= '$additional',
					 ANNEXES	= '$annexes'
				 WHERE ID = '$xmlid'")
    or die('Could not update data in Master table with step 7 data!!!!<br/><center><a href="index.html">Go Back</a></center>'.mysqli_error($link));
}

#Close the connection with the database
mysqli_close($link);
#Delete the uploaded file
unlink($xml);
echo '<center><img src="./images/cv_top_banner1.jpg" alt="Europass CV" /></center><br/><HR size="2"/><br/>';
echo 'The XML data have been loaded in the database successfuly.<br/>';
echo '<a href="index.html">Go Back</a>';
?>