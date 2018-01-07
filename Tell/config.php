<?php

/**
* Database settings
* 
**/

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); // у╙╩╬╧у ╟с▐╟┌╧╔
define('DB_PASSWORD', 'password'); // ▀су╔ ╟су╤ц╤
define('DB_DATABASE', 'cdemos_tells'); // ╟╙у ╟с▐╟┌╧╔

//======================================================  v1.1  MySQLi  ENGINE
$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD,DB_DATABASE);

/*
 * This is the "official" OO way to do it,
 * BUT $connect_error was broken until PHP 5.2.9 and 5.3.0.
 *//* check connection */
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
} else {
  $mysqli->query("SET NAMES 'utf8'");
}


function InsertData(&$mysqli,$phone,$site,$querystring,$category,$city,$ad_ID,$ad_text,$ad_title,$ad_user,$twitter,$sms)
{
            #Insert a new Record
			//$phone = mysql_real_escape_string($phone);
			$result = $mysqli->query("SELECT * FROM `ads` WHERE ad_ID = '$ad_ID' AND site ='$site'") or die( $mysqli->connect_error);
        if (!mysqli_num_rows($result) > 0 ) {
				$query = $mysqli->query("INSERT INTO `ads` (phone,site,created,querystring,category,city,ad_ID,ad_text,ad_title,ad_user,twitter,sms) 
				VALUES ('$phone','$site', NOW(),'$querystring',
				'$category',
				'$city',$ad_ID,
				'$ad_text',
				'$ad_title',
				'$ad_user',
				$twitter,$sms )") or die( $mysqli->connect_error);
				return $query;
        } else
		      return 0;
}
// ======================= //
// Phone Number formatting
//=========================//
function getNumber($number){
	$pattern = '/^(05|\+9665|009665|9665)(\d{8})/';
	$replace = '9665\2';
	//================================
	$pattern1 = '/^(971|\+971|00971)(\d{9})/';
	$replace1 = '971\2';
	//=================================
	$pattern2 = '/^(968|\+968|00968)(\d{9})/';
	$replace2 = '968\2';
	//==================================
	$pattern3 = '/^(974|\+974|00974)(\d{8})/';
	$replace3 = '974\2';
	//==================================
	$pattern4 = '/^(965|\+965|00965)(\d{8})/';
	$replace4 = '965\2';
	


	if(preg_match($pattern, $number, $match1)){
		return preg_replace($pattern, $replace, $match1[0],1);
	} else if(preg_match($pattern1,$number, $match2)){
		return preg_replace($pattern1, $replace1, $match2[0],1);
	} else if(preg_match($pattern2, $number, $match3)){
		return preg_replace($pattern2, $replace2, $match3[0],1);
	} else if(preg_match($pattern3, $number, $match4)){
		return preg_replace($pattern3, $replace3 , $match4[0],1);
	}else if(preg_match($pattern4, $number, $match5)){
		return preg_replace($pattern4, $replace4 , $match5[0],1);
	}

}
//======================================
//           CURL  Function
//======================================
# Define how your webbot will appear in server logs
define("WEBBOT_NAME", "Googlebot");  //USER AGENT 

# Length of time cURL will wait for a response (seconds)
define("CURL_TIMEOUT", 25);

define('DS', DIRECTORY_SEPARATOR);
# Location of your cookie file. (Must be fully resolved local address)
define("COOKIE_FILE", dirname(__FILE__).DS."cookie.txt");

# DEFINE METHOD CONSTANTS
define("HEAD", "HEAD");
define("GET",  "GET");
define("POST", "POST");

# DEFINE HEADER INCLUSION
define("EXCL_HEAD", FALSE);
define("INCL_HEAD", TRUE);
function http($target, $ref, $method, $data_array, $incl_head){
    # Initialize PHP/CURL handle
	$ch = curl_init();
    # Prcess data, if presented
    if(is_array($data_array)){
	    # Convert data array into a query string (ie animal=dog&sport=baseball)
        foreach ($data_array as $key => $value){
            if(strlen(trim($value))>0)
                $temp_string[] = $key . "=" . urlencode($value);
            else
                $temp_string[] = $key;
        }
        $query_string = join('&', $temp_string);
    }
    # HEAD method configuration
    if($method == HEAD){
    	curl_setopt($ch, CURLOPT_HEADER, TRUE);                // No http head
	    curl_setopt($ch, CURLOPT_NOBODY, TRUE);                // Return body
    } else {
        # GET method configuration
        if($method == GET){
            if(isset($query_string))
                $target = $target . "?" . $query_string;
            curl_setopt ($ch, CURLOPT_HTTPGET, TRUE); 
            curl_setopt ($ch, CURLOPT_POST, FALSE); 
        }
        # POST method configuration
        if($method == POST){
            if(isset($query_string))
            curl_setopt ($ch, CURLOPT_POSTFIELDS, $query_string);
            curl_setopt ($ch, CURLOPT_POST, TRUE); 
            curl_setopt ($ch, CURLOPT_HTTPGET, FALSE); 
        }
    	curl_setopt($ch, CURLOPT_HEADER, $incl_head);   // Include head as needed
	    curl_setopt($ch, CURLOPT_NOBODY, FALSE);        // Return body
    }
    
	curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
	curl_setopt($ch, CURLOPT_COOKIEJAR, COOKIE_FILE);   // Cookie management.
	curl_setopt($ch, CURLOPT_COOKIEFILE, COOKIE_FILE);
	curl_setopt($ch, CURLOPT_TIMEOUT, CURL_TIMEOUT);    // Timeout
	curl_setopt($ch, CURLOPT_USERAGENT, WEBBOT_NAME);   // Webbot name
	curl_setopt($ch, CURLOPT_URL, $target);             // Target site
	curl_setopt($ch, CURLOPT_REFERER, $ref);            // Referer value
	curl_setopt($ch, CURLOPT_VERBOSE, FALSE);           // Minimize logs
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);    // No certificate
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);     // Follow redirects
	curl_setopt($ch, CURLOPT_MAXREDIRS, 4);             // Limit redirections to four
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);     // Return in string
    
    # Create return array
    $return_array['FILE']   = curl_exec($ch); 
    $return_array['STATUS'] = curl_getinfo($ch);
    $return_array['ERROR']  = curl_error($ch);
    $return_array['HTTP_CODE']  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    # Close PHP/CURL handle
  	curl_close($ch);
    
    # Return results
  	return $return_array;
}


?>