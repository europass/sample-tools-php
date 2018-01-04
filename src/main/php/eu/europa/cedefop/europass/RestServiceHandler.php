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

namespace eu\europa\cedefop\europass;

class RestServiceHandler
{
    static private $instance = null;
    static private $PDF_TO_XML_EXTRACT_URL = 'https://europass.cedefop.europa.eu/rest/v1/document/extraction';
    static private $CONTENT_TYPE = 'application/pdf';
    static private $ACCEPT_TYPE = 'application/xml';

    /**
     * Class constructor
     *
     */
    private function __construct(){}


    /**
     * Creates a new class instance if none, and always call that same instance
     *
     * @return <RestServiceHandler> instance
     */
    static public function getInstance() {
        if (null === self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function configureServiceAttributes($uploadPath, $outputFilename, $data) {
        $serviceAttributes = array();
        $serviceAttributes['content-type'] = self::$CONTENT_TYPE;
        $serviceAttributes['accept'] = self::$ACCEPT_TYPE;
        $serviceAttributes['url'] = self::$PDF_TO_XML_EXTRACT_URL;
        $serviceAttributes['data'] = $data;
        $serviceAttributes['content-length'] = strlen($data);
        $serviceAttributes['outputFile'] = $uploadPath.$outputFilename;

        return @$serviceAttributes;
    }

    public function xmlExtractFromPDF($fileInput, $uploadPath, $outputFilename){

        // Get data from user input file
        $data = file_get_contents($fileInput);
        $serviceAttributes = self::configureServiceAttributes($uploadPath, $outputFilename, $data);

        $requestResult = array();

        // init curl
        $ch = curl_init();

        // set curl options
        curl_setopt($ch, CURLOPT_URL, $serviceAttributes['url']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array('Content-Type: '.$serviceAttributes['content-type'],
                'Content-length: '.$serviceAttributes['content-length'],
                'Accept: '.$serviceAttributes['accept'],
            )
        );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $serviceAttributes['data']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // execute request
        $server_output = curl_exec($ch);

        //get status, errormessage and close
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errmsg = curl_error($ch);
        curl_close($ch);

        // Store output file to path
        if(false !== file_put_contents($serviceAttributes['outputFile'], $server_output))
            $requestResult['output'] = $serviceAttributes['outputFile'];

        $requestResult['status'] = $http_status;
        $requestResult['message'] = $errmsg;

        return $requestResult;
    }
}