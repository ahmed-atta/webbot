<?php
$target   = "https://www.facebook.com/login.php?login_attempt=1";			
	$ref	  =  "http://www.google.com";				
	$method	  = "POST";        
	$data_array["lsd"] = "AVoErr8_";  
    $data_array['email'] = "ahmed_elfaris@yahoo.com";
	$data_array['pass'] = "farw402203";
	$data_array['default_persistent'] = 0;
	$data_array['persistent'] =1;
	$data_array['timezone'] =-120;
	$data_array['lgnrnd'] = "132354_4_jB";
	$data_array['lgnjs'] = time();
	$data_array['locale'] = "en_US";
	
function cURL($url, $header=NULL, $cookie=NULL, $data_array=NULL)
{
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
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, $header);
    curl_setopt($ch, CURLOPT_NOBODY, $header);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    if ($data_array) {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_array);
    }
    $result = curl_exec($ch);
    if ($result) {
        return $result;
    } else {
        return curl_error($ch);
    }
    curl_close($ch);
}
$a = cURL($target,true,null,$data_array);
preg_match('%Set-Cookie: ([^;]+);%',$a,$b);
$c = cURL($target,true,$b[1],$data_array);
preg_match_all('%Set-Cookie: ([^;]+);%',$c,$d);
$cookie = '';
for($i=0;$i<count($d[0]);$i++)
    $cookie.=$d[1][$i].";";
/*
NOW TO JUST OPEN ANOTHER URL EDIT THE FIRST ARGUMENT OF THE FOLLOWING FUNCTION.
TO SEND SOME DATA EDIT THE LAST ARGUMENT.
*/
 echo cURL("http://www.facebook.com/",null,$cookie,null);

 //if(isset($_POST['q'])) {
    
   	
	// require_once("LIB/LIB_http.php");
	// $incl_head =true;
	// $web_page = http($target,$ref,$method,$data_array,true); // LIB_http
	// print_r($web_page);
//}
 
 
?>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title>Scrapping images</title>
  </head>
  <body>
  <form name='getImages' action ="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
  URL : <input type="text" name="q" />
  <input type="submit" value="Search" name="search" />
  </form>
  
  </body>
 </html>