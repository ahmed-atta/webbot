<?
/*
   Author:	Jamie Telin (jamie.telin@gmail.com), currently at employed Zebramedia.se
   Scriptname: W3C Validation Api v1.0 (W3C Markup Validation Service)
   Use:		
   		//Create new object
			$validate = new W3cValidateApi;
				
			//Example 1
				$validate->setUri('http://google.com/');	//Set URL to check
				echo $validate->makeValidationCall();		//Will return SOAP 1.2 response

			//Example 2
				$a = $validate->validate('http://google.com/');
				if($a){
					echo 'Verified!';
				} else {
					echo 'Not verified!<br>';
					echo 'Errors found: ' . $validate->ValidErrors;
				}
			
			//Example 3
				$validate->ui_validate('http://google.com/'); //Visual display
			
			//Settings
				$validate->Output 		//Set the type of output you want, default = soap12 or web
				$validate->Uri 			//Set url to be checked
				$validate->setUri($uri) //Set url to be checked and make callUrl, deafault way to set URL
				$validate->SilentUi		//Set to false to prevent echo the vidual display
				$validate->Sleep		//Default sleeptime is 1 sec after API call
*/

class W3cValidator
{
	var $BaseUrl = 'http://validator.w3.org/check';
	var $Output = 'soap12';
	var $Uri = '';
	var $Feedback;
	var $CallUrl = '';
	var $ValidResult = false;
	var $ValidErrors = 0;
	var $Sleep = 1;
	var $SilentUi = false;
	var $Ui = '';

	public function __construct()
	{
		//Nothing...
	}

	private function _setUri($uri)
	{
		$this->Uri = $uri;
		//$this->makeCallUrl();
		$this->CallUrl = $this->BaseUrl . "?output=" . $this->Output . "&uri=" . $this->Uri;
	}
		/* gets the data from a URL */
	function get_data($url) 
	{
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
	public function makeValidationCall($uri)
	{
		$this->_setUri($uri);
		if($this->CallUrl != '' && $this->Uri != '' && $this->Output != ''){
			$contents = $this->get_data($this->CallUrl);
			$this->Feedback = $contents;
			sleep($this->Sleep);
			return $contents;
		} else {
			return false;
		}
	}
	
	public function validate($uri)
	{
		$this->makeValidationCall($uri);

		$a = strpos($this->Feedback, '<m:validity>', 0)+12;
		$b = strpos($this->Feedback, '</m:validity>', $a);
		$result = substr($this->Feedback, $a, $b-$a);
		if($result == 'true'){
			$result = true;
		} else {
			$result = false;
		}
		$this->ValidResult = $result;
		
		if($result){
			return $result;
		} else {
			//<m:errorcount>3</m:errorcount>
			$a = strpos($this->Feedback, '<m:errorcount>', $a)+14;
			$b = strpos($this->Feedback, '</m:errorcount>', $a);
			$errors = substr($this->Feedback, $a, $b-$a);
			return $errors;
		}
	}
	
	function ui_validate($uri){
		$this->validate($uri);
		
		if($this->ValidResult){
			$msg1 = 'This document was successfully checked';
			$color1 = '#00CC00';
		} else {
			$msg1 = 'Errors found while checking this document';
			$color1 = '#FF3300';
		}
		$ui = '<div style="background:#FFFFFF; border:1px solid #CCCCCC; padding:2px;">
					<h1 style="color:#FFFFFF; border-bottom:1px solid #CCCCCC; margin:0; padding:5px; background:'.$color1.'; font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:bold;">
					 '.$msg1.'
					</h1>
					<div>
						<strong>Errors:</strong><br>
						'.$this->ValidErrors.'
					</div>
				</div>';
		$this->Ui = $ui;
		if($this->SilentUi == false){
			echo $ui;
		}
		return $ui;
		
	}
	
	/**
     * Parse an XML response from the validator
     * 
     * This function parses a SOAP 1.2 response xml string from the validator.
     *
     * @param string $xml The raw soap12 XML response from the validator.
     * 
     * @return mixed object Services_W3C_HTMLValidator_Response | bool false
     */
	 static function parseSOAP12Response($xml)
    {
        $doc = new DOMDocument();
        if ($doc->loadXML($xml)) {
            $response = array();
            
            // Get the standard CDATA elements
            foreach (array('uri','checkedby','doctype','charset') as $var) {
                $element = $doc->getElementsByTagName($var);
                if ($element->length) {
                    $response[$var] = $element->item(0)->nodeValue;
                }
            }
            // Handle the bool element validity
            $element = $doc->getElementsByTagName('validity');
            if ($element->length &&
                $element->item(0)->nodeValue == 'true') {
                $response['validity'] = true;
            } else {
                $response['validity'] = false;
            }
            if (!$response['validity']) {
                $errors = $doc->getElementsByTagName('error');
                foreach ($errors as $error) {
                    $response['errors'][] = $error;
                }
            }
            $warnings = $doc->getElementsByTagName('warning');
            foreach ($warnings as $warning) {
                $response['warnings'][] = $warning;
            }
            return $response;
        } else {
            // Could not load the XML.
            return false;
        }
    }
}
?>