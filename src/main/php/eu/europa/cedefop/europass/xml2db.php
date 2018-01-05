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

# Get from the XML all the elements with tag name 'Identification' and load them in a list.
$identifications = $doc->getElementsByTagName("Identification");
$gender = NULL;
foreach($identifications as $identification)
{
    /* For each on of the list elements get the various elements included in the identification entity
     * and load them in the corresponding variables. */
    if ($identification->getElementsByTagName("FirstName") && $identification->getElementsByTagName("FirstName")->item(0)) {
        $firstname    = $identification->getElementsByTagName("FirstName")->item(0)->nodeValue;
    } else {$firstname = NULL;}
    if ($identification->getElementsByTagName("Surname") && $identification->getElementsByTagName("Surname")->item(0)) {
        $lastname     = $identification->getElementsByTagName("Surname")->item(0)->nodeValue;
    } else {$lastname = NULL;}
    if ($identification->getElementsByTagName("AddressLine") && $identification->getElementsByTagName("AddressLine")->item(0)) {
        $addressLine  = $identification->getElementsByTagName("AddressLine")->item(0)->nodeValue;
    } else {$addressLine = NULL;}
    if ($identification->getElementsByTagName("Municipality") && $identification->getElementsByTagName("Municipality")->item(0)) {
        $municipality = $identification->getElementsByTagName("Municipality")->item(0)->nodeValue;
    } else {$municipality = NULL;}
    if ($identification->getElementsByTagName("PostalCode") && $identification->getElementsByTagName("PostalCode")->item(0)) {
        $postalCode   = $identification->getElementsByTagName("PostalCode")->item(0)->nodeValue;
    } else {$postalCode = NULL;}
    if ($identification->getElementsByTagName("ContactInfo") && $identification->getElementsByTagName("ContactInfo")->item(0)) {
        $contactInfoNode = $identification->getElementsByTagName("ContactInfo")->item(0);
        if ($contactInfoNode->getElementsByTagName("Country")) {
            $country    = $contactInfoNode->getElementsByTagName("Country")->item(0);
            if ($country != null && $country->getElementsByTagName("Code") && $country->getElementsByTagName("Code")->item(0)) {
                $code  = $country->getElementsByTagName("Code")->item(0)->nodeValue;
            } else {$code = NULL;}
            if ($country != null && $country->getElementsByTagName("Label") && $country->getElementsByTagName("Label")->item(0)) {
                $label = $country->getElementsByTagName("Label")->item(0)->nodeValue;
            } else {$label = NULL;}
        }
        else {$code = NULL; $label = NULL;}
    } else {
        $code = NULL;
        $label = NULL;
    }
    if ($identification->getElementsByTagName("TelephoneList")) {
        $telephones = $identification->getElementsByTagName("TelephoneList");
        if ($telephones->length < 1) {$telephone=NULL; $telephone2=NULL; $telephone3=NULL;}
        foreach ($telephones as $telephone) {
            $index = 0;
            foreach ($identification->getElementsByTagName("Telephone") as $telephoneNode) {
                if ($index == 0 && $telephoneNode->getElementsByTagName("Contact")) {
                    $telephone = $telephoneNode->getElementsByTagName("Contact")->item(0)->nodeValue;
                }
                if ($index == 1 && $telephoneNode->getElementsByTagName("Contact")) {
                    $telephone2 = $telephoneNode->getElementsByTagName("Contact")->item(0)->nodeValue;
                }
                else {$telephone2 = NULL;}
                if ($index == 2 && $telephoneNode->getElementsByTagName("Contact")) {
                    $telephone3 = $telephoneNode->getElementsByTagName("Contact")->item(0)->nodeValue;
                }
                else {$telephone3 = NULL;}
                $index ++;
            }
        }
    }
    if ($identification->getElementsByTagName("Email") && $identification->getElementsByTagName("Email")->item(0)) {
        $email      = $identification->getElementsByTagName("Email")->item(0)->nodeValue;
    } else {$email = NULL;}
    if ($identification->getElementsByTagName("Gender") && $identification->getElementsByTagName("Gender")->item(0)) {
        $genderNode     = $identification->getElementsByTagName("Gender")->item(0);
        if ($genderNode->getElementsByTagName("Code") && $identification->getElementsByTagName("Code")->item(0)) {
            $gender =  $genderNode->getElementsByTagName("Code")->item(0)->nodeValue;
        }
        else {$gender = NULL;}
    } else {$gender = NULL;}
    if ($identification->getElementsByTagName("Birthdate")) {
        if ($identification->getElementsByTagName('Birthdate')->length <1) { $birthdate = NULL; }
        foreach ($identification->getElementsByTagName('Birthdate') as $birthday) {
            $birthdate = $birthday->getAttribute('year') . '-' . trim($birthday->getAttribute('month'),'-') . '-' . trim($birthday->getAttribute('day'),'-');
        }
    } else {$birthdate = NULL;}
    if ($identification->getElementsByTagName("Photo") && $identification->getElementsByTagName("Photo")->item(0)) {
        $photoNode      = $identification->getElementsByTagName("Photo")->item(0);
        if ($photoNode->getElementsByTagName("Data") && $photoNode->getElementsByTagName("Data")->item(0)) {
            $photo =  $photoNode->getElementsByTagName("Data")->item(0)->nodeValue;
            $photo_type =  $photoNode->getElementsByTagName("MimeType")->item(0)->nodeValue;
        }
    } else {$photo = NULL; $photo_type = NULL;}

    #Insert the first set of data in mob_xml table
    mysqli_query($link,"INSERT INTO mob_xml (ID,  FNAME, LNAME, ADDRESS, MUNIC, POSTAL_CODE, CODE_COUNTRY, COUNTRY, PHONE,
									  PHONE2, PHONE3, EMAIL, GENDER, BIRTHDATE, PHOTO_TYPE, PHOTO)
							  VALUES (NULL,'$firstname','$lastname','$addressLine','$municipality','$postalCode','$code','$label',
									  '$telephone','$telephone2','$telephone3','$email','$gender','$birthdate','$photo_type','$photo')")
                or die('Could not insert data in Master XML table!!!!<br/><center><a href="index.html">Go Back</a></center>'.mysqli_error($link));

    /* Retrive the generated id for the insert.
     * We will use it later to update the master table along with the detail ones with the rest of the data.
     */
    //$xmlid = mysql_insert_id();
    $xmlid = mysqli_insert_id($link);

    # Load the different nationalities in the coresponding variables
    $nationalities = $identification->getElementsByTagName("Nationality");
    /* For each on of the list elements get the various elements included in the nationality entity
     * and load them in the coresponding variables. */
    foreach ($nationalities as $nationality) {
        if ($nationality->getElementsByTagName("Code")->item(0)) {
            $ncode  = $nationality->getElementsByTagName("Code")->item(0)->nodeValue;
        } else {
            $ncode = NULL;
        }
        if ($nationality->getElementsByTagName("Label")->item(0)) {
            $nlabel = $nationality->getElementsByTagName("Label")->item(0)->nodeValue;
        } else {
            $nlabel = NULL;
        }

        #Insert the data in the mob_nationality table
        mysqli_query($link,"INSERT INTO mob_nationality (ID, XML_ID, CODE, NATIONALITY)
										  VALUES (NULL,'$xmlid','$ncode','$nlabel')")
                    or die('Could not insert data in Nationality Table<br/><center><a href="index.html">Go Back</a></center>'.mysqli_error($link));
    }
}


# Get from the XML all the elements with tag name 'Headline' and load them in a list.
$applications = $doc->getElementsByTagName("Headline");
if ($doc->getElementsByTagName("Headline")) {

    if ($doc->getElementsByTagName("Headline") && $doc->getElementsByTagName("Headline")->item(0)) {
        $headlineTypeNode = $doc->getElementsByTagName("Headline")->item(0);

        if ($headlineTypeNode->getElementsByTagName("Code") && $headlineTypeNode->getElementsByTagName("Code")->item(0)) {
            $appcode = $headlineTypeNode->getElementsByTagName("Code")->item(0)->nodeValue;
        }
        else { $appcode = NULL;}
        if ($headlineTypeNode->getElementsByTagName("Label") && $headlineTypeNode->getElementsByTagName("Label")->item(0)) {
            $applabel = $headlineTypeNode->getElementsByTagName("Label")->item(0)->nodeValue;
        }
        else { $applabel = NULL;}
        if ($headlineTypeNode->getElementsByTagName("Description") && $headlineTypeNode->getElementsByTagName("Description")->item(0)) {
            $appDescrNode = $headlineTypeNode->getElementsByTagName("Description")->item(0);
            $appDescrlabel = $appDescrNode->getElementsByTagName("Label")->item(0)->nodeValue;
        }

        #Update the data in the mob_xml table
        mysqli_query($link,"UPDATE mob_xml
					SET CODE_APPLICATION = '$appcode',
						APPLICATION		 = '$appDescrlabel'
				  WHERE ID = '$xmlid'") or die('Could not update Master Table with Application data!!!<br/><center><a href="index.html">Go Back</a></center>'.mysqli_error($link));
    }
}

# Get from the XML all the elements with tag name 'WorkExperience' and load them in a list.
$workexperiencelist = $doc->getElementsByTagName("WorkExperience");
if ($workexperiencelist->length > 0)
    /* For each on of the list elements get the various elements included in the WorkExperience entity
     * and load them in the coresponding variables. */
    foreach ($workexperiencelist as $workexperience) {

        $froms = $workexperience->getElementsByTagName("From");
        if ($froms->length <1) { $fyear = NULL; $fmonth = NULL; $fday = NULL;}
        foreach ($froms as $from) {
            if ($from->getAttribute('year')) { $fyear = $from->getAttribute('year');}
            else {$fyear = NULL;}
            if ($from->getAttribute('month')) { $fmonth = trim($from->getAttribute('month'),'-');}
            else {$fmonth = NULL;}
            if ($from->getAttribute('day')) { $fday = trim($from->getAttribute('day'), '-');}
            else {$fday = NULL;}
        }

        $tos = $workexperience->getElementsByTagName("To");
        if ($tos->length <1 ) {$tyear = NULL; $tmonth = NULL; $tday = NULL;}
        foreach ($tos as $to) {
            if ($to->getAttribute('year')) { $tyear = $to->getAttribute('year');}
            else {$tyear = NULL;}
            if ($to->getAttribute('month')) { $tmonth = trim($to->getAttribute('month'),'-');}
            else {$tmonth = NULL;}
            if ($to->getAttribute('day')) { $tday = trim($to->getAttribute('day'), '-');}
            else {$tday = NULL;}
        }

        $positions = $workexperience->getElementsByTagName("Position");
        $plabel = NULL;
        foreach ($positions as $position) {
            if($position->getElementsByTagName("Label")->item(0)) {
                $plabel = $position->getElementsByTagName("Label")->item(0)->nodeValue;
            } else {$plabel = NULL;}
        }
        if ($workexperience->getElementsByTagName("Activities")->item(0)) {
            $wactivities = $workexperience->getElementsByTagName("Activities")->item(0)->nodeValue;
        } else {$wactivities = NULL;}
        if ($workexperience->getElementsByTagName("Name")->item(0)) {
            $wname = $workexperience->getElementsByTagName("Name")->item(0)->nodeValue;
        } else {$wname = NULL;}
        if ($workexperience->getElementsByTagName("AddressLine")->item(0)) {
            $waddress = $workexperience->getElementsByTagName("AddressLine")->item(0)->nodeValue;
        } else {$waddress = NULL;}
        if ($workexperience->getElementsByTagName("Municipality")->item(0)) {
            $wcity = $workexperience->getElementsByTagName("Municipality")->item(0)->nodeValue;
        } else {$wcity = NULL;}
        if ($workexperience->getElementsByTagName("PostalCode")->item(0)) {
            $wpcode = $workexperience->getElementsByTagName("PostalCode")->item(0)->nodeValue;
        } else {$wpcode = NULL;}
        $countries = $workexperience->getElementsByTagName("Country");
        if ($countries->length <1 ) {$weccode = NULL; $weclabel = NULL;}
        $weclabel = NULL;
        foreach ($countries as $country) {
            if($country->getElementsByTagName("Code")->item(0)) {
                $weccode = $country->getElementsByTagName("Code")->item(0)->nodeValue;
            } else {$weccode = NULL;}
            if($country->getElementsByTagName("Label")->item(0)) {
                $weclabel = $country->getElementsByTagName("Label")->item(0)->nodeValue;
            } else {$weclabel = NULL;}
        }
        $sectors = $workexperience->getElementsByTagName("Sector");
        if ($sectors->length <1 ) {$weseccodelabel = NULL;}
        $weseccodelabel = NULL;
        foreach ($sectors as $sector) {
            if($sector->getElementsByTagName("Label")->item(0)) {
                $weseccodelabel = $sector->getElementsByTagName("Label")->item(0)->nodeValue;
            } else {$weseccodelabel = NULL;}
        }

        #Insert the data in the mob_work_experience table
        mysqli_query($link,"INSERT INTO mob_work_experience (ID, XML_ID, DAY_FROM, MONTH_FROM, YEAR_FROM, DAY_TO, MONTH_TO, YEAR_TO,
													  WPOSITION, ACTIVITIES, EMPLOYER_NAME, EMPLOYER_ADDRESS,
													  EMPLOYER_MUNIC, EMPLOYER_ZCODE, CODE_COUNTRY, COUNTRY, SECTOR)
										  VALUES     (NULL,'$xmlid','$fday','$fmonth','$fyear','$tday','$tmonth','$tyear','$plabel','$wactivities',
													  '$wname','$waddress','$wcity','$wpcode','$weccode','$weclabel','$weseccodelabel' )")
        or die('Could not insert data in Work Experience table!!!!<br/><center><a href="index.html">Go Back</a></center>'.mysqli_error($link));

    }

# Get from the XML all the elements with tag name 'Education' and load them in a list.
$educationlist = $doc->getElementsByTagName("Education");
if ($educationlist->length > 0)
    /* For each on of the list elements get the various elements included in the Education entity
     * and load them in the coresponding variables. */
    foreach ($educationlist as $education) {

        $efroms = $education->getElementsByTagName("From");
        if ($efroms->length <1) { $efyear = NULL; $efmonth = NULL; $efday = NULL;}
        foreach ($efroms as $efrom) {
            if ($efrom->getAttribute('year')) { $efyear = $efrom->getAttribute('year');}
            else {$efyear = NULL;}
            if ($efrom->getAttribute('month')) { $efmonth = trim($efrom->getAttribute('month'),'-');}
            else {$efmonth = NULL;}
            if ($efrom->getAttribute('day')) { $efday = trim($efrom->getAttribute('day'), '-');}
            else {$efday = NULL;}
        }

        $etos = $education->getElementsByTagName("To");
        if ($etos->length <1) { $etyear = NULL; $etmonth = NULL; $etday = NULL;}
        foreach ($etos as $eto) {
            if ($eto->getAttribute('year')) { $etyear = $eto->getAttribute('year');}
            else {$etyear = NULL;}
            if ($eto->getAttribute('month')) { $etmonth = trim($eto->getAttribute('month'),'-');}
            else {$etmonth = NULL;}
            if ($eto->getAttribute('day')) { $etday = trim($eto->getAttribute('day'), '-');}
            else {$etday = NULL;}
        }
        if ($education->getElementsByTagName("Title")->item(0)) {
            $title = mysqli_real_escape_string($link, $education->getElementsByTagName("Title")->item(0)->nodeValue);
        } else {$title = NULL;}
        if ($education->getElementsByTagName("Activities")->item(0)) {
            $eskills = $education->getElementsByTagName("Activities")->item(0)->nodeValue;
        } else {$eskills = NULL;}

        if ($education->getElementsByTagName("Name")->item(0)) {
            $ename = $education->getElementsByTagName("Name")->item(0)->nodeValue;
        } else {$ename = NULL;}
        if ($education->getElementsByTagName("AddressLine")->item(0)) {
            $eaddress = $education->getElementsByTagName("AddressLine")->item(0)->nodeValue;
        } else {$eaddress = NULL;}
        if ($education->getElementsByTagName("Municipality")->item(0)) {
            $ecity = $education->getElementsByTagName("Municipality")->item(0)->nodeValue;
        } else {$ecity = NULL;}
        if ($education->getElementsByTagName("PostalCode")->item(0)) {
            $epcode = $education->getElementsByTagName("PostalCode")->item(0)->nodeValue;
        } else {$epcode = NULL;}

        $ecountries = $education->getElementsByTagName("Country");
        $educlabel = NULL;
        foreach ($ecountries as $ecountry) {
            if($ecountry->getElementsByTagName("Code")->item(0)) {
                $educcode = $ecountry->getElementsByTagName("Code")->item(0)->nodeValue;
            } else {$educcode = NULL;}
            if($ecountry->getElementsByTagName("Label")->item(0)) {
                $educlabel = $ecountry->getElementsByTagName("Label")->item(0)->nodeValue;
            } else {$educlabel = NULL;}
        }

        $levels = $education->getElementsByTagName("Level");
        $edullabel = NULL;
        foreach ($levels as $level) {
            if($level->getElementsByTagName("Code")->item(0)) {
                $edulcode = $level->getElementsByTagName("Code")->item(0)->nodeValue;
            } else {$edulcode = NULL;}
            if($level->getElementsByTagName("Label")->item(0)) {
                $edullabel = $level->getElementsByTagName("Label")->item(0)->nodeValue;
            } else {$edullabel = NULL;}
        }

        $edufields = $education->getElementsByTagName("Field");
        $eduflabel = NULL;
        foreach ($edufields as $edufield) {
            if($edufield->getElementsByTagName("Code")->item(0)) {
                $edufcode = $edufield->getElementsByTagName("Code")->item(0)->nodeValue;
            } else {$edufcode = NULL;}
            if($edufield->getElementsByTagName("Label")->item(0)) {
                $eduflabel = $edufield->getElementsByTagName("Label")->item(0)->nodeValue;
            } else {$eduflabel = NULL;}
        }


        #Insert the data in the mob_education table
        mysqli_query($link,"INSERT INTO mob_education (ID, XML_ID, TITLE, SUBJECT, ORG_NAME, ORG_ADDRESS, ORG_MUNIC,
											    ORG_ZCODE, CODE_COUNTRY, COUNTRY, CODE_LEVEL, EDULEVEL,CODE_EDU_FIELD,EDU_FIELD,
												DAY_FROM, MONTH_FROM, YEAR_FROM, DAY_TO, MONTH_TO, YEAR_TO)
										VALUES (NULL,'$xmlid','$title','$eskills','$ename','$eaddress','$ecity','$epcode',
												'$educcode','$educlabel','$edulcode','$edullabel','$edufcode','$eduflabel',
												'$efday','$efmonth','$efyear','$etday','$etmonth','$etyear')")
                    or die('Could not insert data in Education table!!!!<br/><center><a href="index.html">Go Back</a></center>'.mysqli_error($link));
    }


$motherLanguagelists = $doc->getElementsByTagName("MotherTongue");
foreach ($motherLanguagelists as $language) {

    if ($language->getElementsByTagName("Code")->item(0)) {
        $mlcode  = $language->getElementsByTagName("Code")->item(0)->nodeValue;
    } else {$mlcode = NULL;}
    $mllabel = $language->getElementsByTagName("Label")->item(0)->nodeValue;

    #Update the data in the mob_xml table with the mother_language
    mysqli_query($link,"UPDATE mob_xml
					         SET CODE_MOTHER_LANGUAGE = '$mlcode',
						         MOTHER_LANGUAGE	  = '$mllabel'
							 WHERE ID = '$xmlid'")
    or die('Could not update Master table with Mother Language!!!!<br/>'.mysqli_error($link));

    break; // Keep only 1st mother language.. We can add new mother language table if we need to store all langs.
}

$foreignLanguagelists = $doc->getElementsByTagName("ForeignLanguage");
foreach ($foreignLanguagelists as $language) {
    if ($language->getElementsByTagName("Code") && $language->getElementsByTagName("Code")->item(0)) {
        $flcode             = $language->getElementsByTagName("Code")->item(0)->nodeValue;
    }
    else { $flcode = NULL; }
    if ($language->getElementsByTagName("Label") && $language->getElementsByTagName("Label")->item(0)) {
        $fllabel            = $language->getElementsByTagName("Label")->item(0)->nodeValue;
    }
    else { $fllabel = NULL; }

    if ($language->getElementsByTagName("Label") && $language->getElementsByTagName("Label")->item(0)) {
        $fllabel            = $language->getElementsByTagName("Label")->item(0)->nodeValue;
    }
    else { $fllabel = NULL; }
    if ($language->getElementsByTagName("Listening") && $language->getElementsByTagName("Listening")->item(0)) {
        $listening          = $language->getElementsByTagName("Listening")->item(0)->nodeValue;
    }
    else {$listening = NULL;}
    if ($language->getElementsByTagName("Reading") && $language->getElementsByTagName("Reading")->item(0)) {
        $reading            = $language->getElementsByTagName("Reading")->item(0)->nodeValue;
    }
    else {$reading = NULL;}
    if ($language->getElementsByTagName("SpokenInteraction") && $language->getElementsByTagName("SpokenInteraction")->item(0)) {
        $spokeninteraction  = $language->getElementsByTagName("SpokenInteraction")->item(0)->nodeValue;
    }
    else {$spokeninteraction = NULL;}
    if ($language->getElementsByTagName("SpokenProduction") && $language->getElementsByTagName("SpokenProduction")->item(0)) {
        $spokenproduction   = $language->getElementsByTagName("SpokenProduction")->item(0)->nodeValue;
    }
    else {$spokenproduction = NULL;}
    if ($language->getElementsByTagName("Writing") && $language->getElementsByTagName("Writing")->item(0)) {
        $writing            = $language->getElementsByTagName("Writing")->item(0)->nodeValue;
    }
    else {$writing = NULL;}


    #Insert the data in the mob_language table
    mysqli_query($link,"INSERT INTO mob_language (ID, XML_ID, CODE_LANGUAGE, OLANGUAGE, LISTENING, READING,
													   SPOKEN_INTERACTION, SPOKEN_PRODUCTION, WRITING)
											   VALUES (NULL,'$xmlid','$flcode','$fllabel','$listening','$reading',
											   		   '$spokeninteraction','$spokenproduction','$writing')")
                    or die('Could not insert data in Other Languages table!!!!<br/><center><a href="index.html">Go Back</a></center>'.mysqli_error($link));
}

# Get from the XML all skills and competences and load them in a list.
$communicList = $doc->getElementsByTagName("Communication");
if ($communicList->length > 0) {
    $social         = trim($communicList->item(0)->nodeValue);
}
else { $social = NULL; }
$organisList = $doc->getElementsByTagName("Organisational");
if ($organisList->length > 0) {
    $organisational = trim($organisList->item(0)->nodeValue);
}
else { $organisational = NULL; }
$jobRelList = $doc->getElementsByTagName("JobRelated");
if ($jobRelList->length > 0) {
    $jobRelated         = trim($jobRelList->item(0)->nodeValue);
}
else { $jobRelated = NULL; }
$computerList = $doc->getElementsByTagName("Computer");
if ($computerList->length > 0) {
    $computer         = trim($computerList->item(0)->nodeValue);
}
else { $computer = NULL; }
$otherList = $doc->getElementsByTagName("Other");
if ($otherList->length > 0) {
    $other         = trim($otherList->item(0)->nodeValue);
}
else { $other = NULL; }

#Update the data in the mob_xml table with the data of the skill section
mysqli_query($link,"UPDATE mob_xml
				 SET SOCIAL           = '$social',
					 ORGANISATIONAL	  = '$organisational',
					 JOB_RELATED	  = '$jobRelated',
					 COMPUTER		  = '$computer',
					 OTHER			  = '$other'
				 WHERE ID = '$xmlid'")
        or die('Could not update data in Master table with skills/competences data!!!!<br/><center><a href="index.html">Go Back</a></center>'.mysqli_error($link));


# Get from the XML all the elements with tag name 'Driving Licence' and load them in a list.
$drivinglist = $doc->getElementsByTagName("Licence");
$drivingcnt = $drivinglist->length;

if ($drivingcnt > 0) {
    for ($idx = 0; $idx < $drivingcnt; $idx++) {
        $driving_licence = $drivinglist->item($idx)->nodeValue;
        #Insert the data in the mob_driving_licence table
        mysqli_query($link,"INSERT INTO mob_driving_licence (ID, XML_ID, DRIVING_SKILL)
											  VALUES (NULL,'$xmlid','$driving_licence')")
        or die('Could not insert data in Driving Skill table!!!!<br/><center><a href="index.html">Go Back</a></center>');
    }
}

$achitems = $doc->getElementsByTagName("Achievement");
$allAdditional = '';
foreach($achitems as $achitem) {
    $achLabel = $achitem->getElementsByTagName("Label")->item(0)->nodeValue;
    $achDescr = $achitem->getElementsByTagName("Description")->item(0)->nodeValue;

    $additional = $achLabel . ' ' . $achDescr;
    $allAdditional = $allAdditional . ' ' . $additional;
}
#Update the data in the mob_xml table with the additional data
mysqli_query($link,"UPDATE mob_xml
				 SET ADDITIONAL	= '$allAdditional'
				 WHERE ID = '$xmlid'")
        or die('Could not update data in Master table with additional data!!!!<br/><center><a href="index.html">Go Back</a></center>'.mysqli_error($link));


$annexitems = $doc->getElementsByTagName("Attachment");
$allAnnexes = '';
foreach($annexitems as $annexitem) {
    #Load the Annexes in the form
    $annexNames = $annexitem->getElementsByTagName("Name");
    foreach($annexNames as $annexname) {
        $annexes = trim($annexNames->item(0)->nodeValue);
        $allAnnexes = $allAnnexes . ' ' .$annexes;

    }
}
#Update the data in the mob_xml table with name of annexes
mysqli_query($link,"UPDATE mob_xml
				 SET ANNEXES	= '$allAnnexes'
				 WHERE ID = '$xmlid'")
        or die('Could not update data in Master table with annexes data!!!!<br/><center><a href="index.html">Go Back</a></center>'.mysqli_error($link));


#Close the connection with the database
mysqli_close($link);
#Delete the uploaded file
unlink($xml);
echo '<center><img src="./images/cv_top_banner1.jpg" alt="Europass CV" /></center><br/><HR size="2"/><br/>';
echo 'The XML data have been loaded in the database successfuly.<br/>';
echo '<a href="index.html">Go Back</a>';
?>