<?php

/**
* Database settings
* 
**/

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); // у╙╩╬╧у ╟с▐╟┌╧╔
define('DB_PASSWORD', 'password'); // ▀су╔ ╟су╤ц╤
define('DB_DATABASE', 'cdemos_twitter'); // ╟╙у ╟с▐╟┌╧╔

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


function InsertData(&$mysqli,$phone,$site,$querystring,$category,$city)
{
            #Insert a new Record
			//$phone = mysql_real_escape_string($phone);
			$result = $mysqli->query("SELECT * FROM `phones` WHERE phone = '$phone' AND category ='$category'") or die( $mysqli->connect_error);
        if (!mysqli_num_rows($result) > 0 ) {
				$query = $mysqli->query("INSERT INTO `phones` (phone,site,created,querystring,category,city) 
				VALUES ('$phone','$site', NOW(),'$querystring','$category','$city' )") or die( $mysqli->connect_error);
				 return $query;
        } else
		      return false;
            
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


?>