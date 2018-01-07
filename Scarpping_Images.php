<?php
// We can search for the character, ignoring anything before the offset
// $newstring = '<div class="fb-like-box" data-href="https://www.facebook.com/itwadi" data-width="180" data-show-faces="false" data-stream="false" data-header="true"></div>';
// $pos = strpos($newstring, 'id='); // $pos = 7, not 0
// if($pos){
  // echo $pos;
// }else
 // echo 0;
 
 
 // include("w3c_validator.class.php");
 // $wv= new W3cValidator();
 // $cont = $wv->makeValidationCall('google.com');
 // $xml = $wv->parseSOAP12Response($cont);
 //$rs = $wv->validate('google.com'); 
 //echo "<xmp>".$rs."</xmp>";
 // print_r($xml);
 if(isset($_POST['q'])) {
    
   	// include('webbot.class.php');
	// $wb  = new webbot('');
	// $search['action'] =  "http://www.bing.com/search";//"http://www.google.com/search";
	// $search['q']= array('q' => $_GET['q']);
	// $search['method'] = "GET";	
	// $search['xpath'] = "//*[@id='results']";// "//*[@id='ires']";
	
	//$report = $wb->analysisURL();
	include('webbot.class.php');
	$web_page = http_get($_POST['q'], ''); // LIB_http
	$siteHtml = $web_page['FILE'];
	//echo "<xmp>".$siteHtml."</xmp>";
	$imgs = parse_array($siteHtml, "<img",">"); // LIB_parse
	foreach($imgs as $key => $ele){
		$img_src = get_attribute($ele,'src');
		//$img_ext = substr($img_src, -3); 
		$img_p = explode("/", $img_src);
		$img_name = $img_p[sizeof($img_p)-1];
		//echo "<xmp>".$ele ."</xmp>";
		//echo $img_name;
	    if(@file_put_contents("images/".$img_name, file_get_contents($img_src))){
			echo "<xmp><img src='images4/".$img_name ."'/></xmp>";
		}
	}
}
 
 
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