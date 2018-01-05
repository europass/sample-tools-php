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
# Get from the XML all the elements with tag name 'Identification' and load them in a list.
$identifications = $doc->getElementsByTagName("Identification");

/* For each on of the list elements get the various elements included in the identification entity
 * and load them in the coresponding variables. */
$gender = NULL;
foreach($identifications as $identification)
{
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
            $birthdate = $birthday->getAttribute('year') . '/ ' . trim($birthday->getAttribute('month'),'-') . '/ ' . trim($birthday->getAttribute('day'),'-');
        }
    } else {$birthdate = NULL;}
    if ($identification->getElementsByTagName("Photo") && $identification->getElementsByTagName("Photo")->item(0)) {
        $photoNode      = $identification->getElementsByTagName("Photo")->item(0);
        if ($photoNode->getElementsByTagName("Data") && $photoNode->getElementsByTagName("Data")->item(0)) {
            $photo =  $photoNode->getElementsByTagName("Data")->item(0)->nodeValue;
            $photo_type =  $photoNode->getElementsByTagName("MimeType")->item(0)->nodeValue;
        }
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

    if ($telephone != NULL || $telephone2 != NULL || $telephone3 != NULL) {
        echo '<label style="font-weight: bold; ">Telephone(s): </label>';
        if ($telephone != NULL)		{echo ' ' . $telephone;}
        if ($telephone2 != NULL)		{echo ' ' . $telephone2;}
        if ($telephone3 != NULL)		{echo ' ' . $telephone3;}
        echo '<br/>';
    }
    if ($email != NULL)			{echo '<label style="font-weight: bold; ">E-mail(s): </label>'.$email.'<br/>';}

    $nationalities = $identification->getElementsByTagName("Nationality");
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
        #Load the Nationality(s) in the form
        if ($nlabel != NULL)	{echo "<b>Nationality: </b>".$nlabel."<br/>";}
    }
}

if ($gender != NULL) {
    if      ($gender == "M") {echo '<label style="font-weight: bold; ">Gender: </label>Male<br/>';}
    else if ($gender == "F") {echo '<label style="font-weight: bold; ">Gender: </label>Female<br/>';}
    else 					 {echo '<label style="font-weight: bold; ">Gender: </label>Not Available<br/>';}
}


# Load the data of the second step, included in the <Headline/ Description> tag.
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

        #Load the Application in the form
        if ($applabel != NULL) {
            echo '<br/><h2>Type of Application</h2><br/><img src="./images/bg_win.jpg" /><br/>';
            echo '<label for"application" style="font-weight: bold; display:block;">'.$applabel.'</label></br><textarea id="additional" rows="5" cols="60" readonly="true">'.$appDescrlabel.'</textarea><br/>';
        }
    }
}

#Load the data of the third step, included in the <workexperiencelist> tag.
$workexperiencelist = $doc->getElementsByTagName("WorkExperience");
if ($workexperiencelist->length > 0)
    echo '<br/><h2>Work Experience</h2><br/><img src="./images/bg_win.jpg" /><br/>';
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

#Load the data of the third step, included in the <Education> tag.
$educationlist = $doc->getElementsByTagName("Education");
if ($educationlist->length > 0)
    echo '<br/><h2>Education and training</h2><br/><img src="./images/bg_win.jpg" /><br/>';
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
        $title = $education->getElementsByTagName("Title")->item(0)->nodeValue;
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

    #Load the Education(s) in the form
    echo '<hr size="1">';
    if ($efday != NULL || $efmonth!= NULL || $efyear != NULL ) { echo "<b>From:</b> ".$efday."/".$efmonth."/".$efyear." ";}
    if ($etday != NULL || $etmonth!= NULL || $etyear != NULL ) { echo "<b>To:</b> ".$etday."/".$etmonth."/".$etyear;}
    echo "<br/>";
    if ($title != NULL)				{echo "<b>Title of qualification awarded: </b>".$title."<br/>";}
    if ($eskills != NULL)			{echo "<b>Principal subjects / occupational skills covered: </b>".$eskills."<br/>";}
    echo "<b>Organisation providing education and training</b><br/>";
    if ($ename != NULL)				{echo "<b>Name: </b>".$ename."<br/>";}
    if ($eaddress != NULL)			{echo "<b>Street number / Street: </b>".$eaddress."<br/>";}
    if ($ecity != NULL)				{echo "<b>City: </b>".$ecity."<br/>";}
    if ($epcode != NULL)			{echo "<b>Postal code: </b>".$epcode."<br/>";}
    if ($educlabel != NULL)			{echo "<b>Country: </b>".$educlabel."<br/><br/>";}
    if ($edullabel != NULL)			{echo "<b>Level in national or international classification: </b>".$edullabel."<br/><br/>";}
    if ($eduflabel != NULL)			{echo "<b>Educational field: </b>".$eduflabel."<br/>";}
    echo "<br/>";
}

#Languages step
echo '<br/><h2>Language(s)</h2><br/><img src="./images/bg_win.jpg" /><br/>';

$motherLanguagelists = $doc->getElementsByTagName("MotherTongue");
if ($motherLanguagelists->length > 0) {
    echo '<h3>Mother tongue(s)</h3></br>';
}
foreach ($motherLanguagelists as $language) {

    if ($language->getElementsByTagName("Code")->item(0)) {
        $mlcode  = $language->getElementsByTagName("Code")->item(0)->nodeValue;
    } else {$mlcode = NULL;}
    $mllabel = $language->getElementsByTagName("Label")->item(0)->nodeValue;

    #Load Mother Language in the form
    if ($mllabel != NULL) {
        echo $mllabel . ' ';
    }
}

$foreignLanguagelists = $doc->getElementsByTagName("ForeignLanguage");
if ($foreignLanguagelists->length > 0) {
    echo '<br/><h3>Other language(s) - Self-assessment</h3><br/>';
}
foreach ($foreignLanguagelists as $language) {
    if ($language->getElementsByTagName("Code") && $language->getElementsByTagName("Code")->item(0)) {
        $flcode             = $language->getElementsByTagName("Code")->item(0)->nodeValue;
    }
    else {$flcode = NULL;}
    if ($language->getElementsByTagName("Label") && $language->getElementsByTagName("Label")->item(0)) {
        $fllabel            = $language->getElementsByTagName("Label")->item(0)->nodeValue;
    }
    else {$fllabel = NULL;}
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
}

#Load the data of the third step, included in the skills and competences
echo '<br/><h2>Personal skills and competences </h2><br/><img src="./images/bg_win.jpg" /><br/>';

$communicList = $doc->getElementsByTagName("Communication");
if ($communicList->length > 0) {
    $social         = trim($communicList->item(0)->nodeValue);
    if ($social != NULL)			{echo '<label for"social" style="font-weight: bold; display:block;">Social skills and competences</label></br><textarea id="social" rows="5" cols="60" readonly="true">'.$social."</textarea><br/><br/>";}
}
$organisList = $doc->getElementsByTagName("Organisational");
if ($organisList->length > 0) {
    $organisational = trim($organisList->item(0)->nodeValue);
    if ($organisational != NULL)	{echo '<label for"organ" style="font-weight: bold; display:block;">Organizational skills and competences</label></br><textarea id="organ" rows="5" cols="60" readonly="true">'.$organisational."</textarea><br/><br/>";}
}
$jobRelList = $doc->getElementsByTagName("JobRelated");
if ($jobRelList->length > 0) {
    $jobRelated         = trim($jobRelList->item(0)->nodeValue);
    if ($jobRelated != NULL)			{echo '<label for"tech" style="font-weight: bold; display:block;">Job related skills and competences</label></br><textarea id="tech" rows="5" cols="60" readonly="true">'.$jobRelated."</textarea><br/><br/>";}
}
$computerList = $doc->getElementsByTagName("Computer");
if ($computerList->length > 0) {
    $computer         = trim($computerList->item(0)->nodeValue);
    if ($computer != NULL)			{echo '<label for"computer" style="font-weight: bold; display:block;">Computer skills and competences</label></br><textarea id="computer" rows="5" cols="60" readonly="true">'.$computer."</textarea><br/><br/>";}
}
$otherList = $doc->getElementsByTagName("Other");
if ($otherList->length > 0) {
    $other         = trim($otherList->item(0)->nodeValue);
    if ($other != NULL)				{echo '<label for"other" style="font-weight: bold; display:block;">Other skills and competences</label></br><textarea id="other" rows="5" cols="60" readonly="true">'.$other."</textarea><br/>";}
}

#Load the data of the third step, included in the <drivinglicence> tag.
$drivinglist = $doc->getElementsByTagName("Licence");
$drivingcnt = $drivinglist->length;

echo '<br/><h3>Driving licence(s)</h3><br/>';
if ($drivingcnt == 0) {echo 'No Driving License.<br/><br/>';}
if ($drivingcnt > 0) {
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

#Load the data of the third step, included in the <Achievement> tag.
$achitems = $doc->getElementsByTagName("Achievement");
if ($achitems-> length > 0) {
    echo '<br/><h2>Additional information </h2><br/><img src="./images/bg_win.jpg" /><br/>';
}
foreach($achitems as $achitem) {
    $achLabel = $achitem->getElementsByTagName("Label")->item(0)->nodeValue;
    $achDescr = $achitem->getElementsByTagName("Description")->item(0)->nodeValue;

    if ($achLabel != NULL) {echo '<label for"additional" style="font-weight: bold; display:block;"></label></br><span>'.$achLabel.'</span></br><textarea id="additional" rows="5" cols="60" readonly="true">'.$achDescr.'</textarea><br/><br/>';}
}

$annexitems = $doc->getElementsByTagName("Attachment");
if ($annexitems-> length > 0) {
    echo '<br/><h2>Annexes </h2><br/><img src="./images/bg_win.jpg" /><br/>';
}
foreach($annexitems as $annexitem) {
    #Load the Annexes in the form
    $annexNames = $annexitem->getElementsByTagName("Name");
    foreach($annexNames as $annexname) {
        $annexes = trim($annexNames->item(0)->nodeValue);
        if ($annexes != NULL)	{echo '<label for"annexes" style="font-weight: bold; display:block;"></label></br><span id="annexes">'.$annexes."</span>";}
    }
}

#Delete the uploaded file
unlink($xml);
echo '<hr size ="2"/>';
echo '<center><a href="index.html">Go Back</a></center>';
echo '</font></form></body></html>';
?>