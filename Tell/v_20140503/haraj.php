<?php

require_once("config.php");
if(isset($_POST['start']) && isset($_POST['end'])){
	$start = (int)$_POST['start'];
	$end = (int) $_POST['end'];
	
	for($i = $start; $i<= $end; $i++ ){
		$contents = @file_get_contents("http://haraj.com.sa/".$i);
		@preg_match('/<span class="red">(.*)<\/span>/i', $contents, $matches); // 0551016333//echo "<xmp>".@$matches[1]."</xmp>";
		@preg_match('/<title>(.*)<\/title>/i', $contents, $title); // 	echo "<xmp>".@$title[1]."</xmp>";
		$category = @explode("|",$title[1]);   //echo "<xmp>".@$category[1]."</xmp>";
		@preg_match('/<b><a href="http:\/\/haraj.com.sa\/city\/(.*)<\/a>/i', $contents, $city); // City //<a href="http://haraj.com.sa/city/جده">جده</a>
		// Ad Text
		//@preg_match("'<div class=\"cont_title\">(.*)<\/div>(.*)<div id=\"leftmenu\">'si", $contents, $ad_text); //
		preg_match("(<div class=\"cont_title\">(.*)<\/div>(.*)<div id=\"leftmenu\">)siU",$contents, $ad_text);
		
		 if(!empty($matches[1]) && strlen($matches[1]) > 5 ){
			$number = getNumber($matches[1]);
			$querystring = $start." - ".$i." - ".$end;
			$ad_ID = $i;
			// ================== Ad_text
	 	    $ads_text = $ad_text[2];
			//=================== Ad_title
			preg_match("'<h1>(.*)<\/h1>'si", $ad_text[1], $ad_title);
			//$post_title = @mysql_real_escape_string();
	 	    $ads_title = $ad_title[1]; //str_replace("'",'"',$post_title);
			//==================== Ad_user
			preg_match("'<a href=\"http:\/\/haraj.com.sa\/users\/(.*)<\/a>'i", $ad_text[1], $ad_user);
	 	    $ads_user = $ad_user[0];
			//===================== IMAGES 
			@preg_match_all("(<img(.*)>)siU",$ads_text,$images);
			//print_r($images[0]);
			$srcs ='';
			foreach($images[0] as $k=>$image){
				@preg_match('/src="[A-Za-z0-9\'?!-\s\/\/\.\:]+"/siU',$image,$src);
				//print_r($src[0]);
				// $f = strripos($image, 'src="');
				// $src = substr($image, $f+5, strlen($image));
				$src = ltrim($src[0],'src=');
				$srcs .= trim($src,'"')."#"; 
			}
			$imgs_str = rtrim($srcs,'#');
			//=================================
			if(empty($category[1]))
				$category[1] = "مطلوب";
				
				$ad_exist_flag = false ;
				$ad_exist = $mysqli->query("SELECT id FROM `ads` WHERE `ad_ID`='$ad_ID';");
				if($ad_exist->num_rows > 0){
					$ad_exist_flag  = 1;
				}
			if(!empty($number) && !$ad_exist_flag){
				$twitterON = (isset($_POST['twitterON']) && $_POST['twitterON'] =='ON')? 1 :0;
				if(isset($_POST['smsOFF']) && $_POST['smsOFF'] == 'ON'){
					$sms = 0;
				}else {
					$smsON = (isset($_POST['smsON']) && $_POST['smsON'] =='OFF')? 0 :1;
					//$adsON = (isset($_POST['adsON']) && $_POST['adsON'] =='OFF')? 0 :1;
					if($smsON){
						$sms = 1;
					} else {
						$mobi = $mysqli->query("SELECT id FROM `ads` WHERE `phone`='$number' AND `sms` = 1 ;");
						if($mobi->num_rows > 0){
							$sms = 0;
						} else {
							$sms = 1;	
						}
					}
				}
				
				InsertData($mysqli,$number,"haraj",$querystring,$category[1],strip_tags($city[0]),$ad_ID,strip_tags($ads_text),strip_tags($ads_title),$ads_user,$twitterON,$sms);
					//============================== INSRT INTO adds
					$m_class = @mb_convert_encoding($category[1], 'UTF-8',mb_detect_encoding($category[1]));
					$m_class = trim($m_class);
					if($m_class == "حراج"){
						$m_class = "حراج#" ;
					} else {
						$m_class = ltrim($m_class,"حراج");
					}
					$m_location = strip_tags($city[0]);
					$m_location = @mb_convert_encoding($m_location, 'UTF-8',mb_detect_encoding($m_location, 'UTF-8, ISO-8859-1', true));
					//echo $m_class."<br/>".$m_location."<br/>";
					
					if($rs_class = $mysqli->query("SELECT *,cs.ID AS csID,mb.ID AS MID  FROM `classes` AS cs,`members` AS mb 
					WHERE cs.m_class LIKE '%$m_class%' AND cs.member_ID = mb.pid ;")){
						$class = $rs_class->fetch_assoc();
					}
					if($rs_location = $mysqli->query("SELECT * FROM `locations` WHERE `m_location` LIKE '%$m_location%' ;")){
						$location = $rs_location->fetch_assoc();
					}
					
					$params = serialize(array('Email'=> '',
															'Phone'=> $number,
															'HidePhone' => 0,
															'HideEmail' =>1,
															'Soom' => 1));
					
					
					//print_r($class);
					//print_r($location);
					//exit;
					
					if(isset($class['csID']) && isset($class['MID']) 
					&& isset($location['country_ID'])
					&& isset($location['city_ID']) ){
					
						$ads_title = trim(strip_tags($ads_title));
						$ads_title = trim($ads_title,"»");
						$ads_title = str_replace(array("\n","\r","&nbsp;"), ' ', $ads_title);
						
						if(isset($_POST['SendTo36']) && $_POST['SendTo36'] =='ON'){
							$arrayData['Name']= $ads_title;
							$arrayData['Details']= strip_tags($ads_text);
							$arrayData['MemberID']= $class['MID'];
							$arrayData['ClassID']= $class['csID'];
							$arrayData['CountryID']= $location['country_ID'];
							$arrayData['CityID']= $location['city_ID'];
							$arrayData['Type'] = 2;
							$arrayData['SellType']= $class['SellType'];
							$arrayData['Active']= 1;
							$arrayData['Params']= $params;
							$arrayData['Album']= $imgs_str;
							$arrayData['twitter']= $twitterON;
							$arrayData['sms']= $sms;
							$arrayData['m_mobile']= $class['mobile'];
							$arrayData['m_name']= $class['name'];
							$arrayData['mb_sms']= $class['mb_sms'];
							$arrayData['smsTime'] = $_POST['smsTime'];
							$reqs = http("http://3rd6lb.com/_R1.php","google.com","POST",$arrayData ,false);
							
						} else {
							$mysqli->query("INSERT INTO `adds` (`Name`,`Details`,`MemberID`,`ClassID`,`CountryID`,
								`CityID`,`Type`,`SellType`,`Active`,`Params`,
								`Album`,`twitter`,`sms`,`m_mobile`,`m_name`,`mb_sms`,`smsTime`) 
										VALUES ('".$ads_title."','".
													strip_tags($ads_text)."',".
													$class['MID'].",".
													$class['csID'].",".
													$location['country_ID'].",".
													$location['city_ID'].",2,".
													$class['SellType'].",1,'$params','$imgs_str',$twitterON,$sms,'".
													$class['mobile']."','".
													$class['name']."','".
													$class['mb_sms']."','".$_POST['smsTime']."');");
					
						}
					}
			}
		
		}
	}
	$msg = "Processing finished ..................";
}

//==================== Last Process ========================== //
$rs_p = $mysqli->query("SELECT querystring FROM `ads` WHERE site = 'haraj' ORDER BY id DESC LIMIT 1;");
    if (mysqli_num_rows($rs_p) > 0 ) {  
		$row = $rs_p->fetch_assoc();
		$qs = $row['querystring'];
		$se = explode('-',$qs);
		$lstart = $se[0];
		$lend = $se[1];
		$fend = $se[2];
	}
	
	
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">
<!-- Optional theme -->
<link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap-theme.min.css">

<style>
.jumbotron{
margin-top: 20px;
}
.col-sm-10 {
	width:20%;
	padding:0px;
}
.col-sm-2 {
width:40%;
font-size:15px;
padding:0px;
}
</style>
</head>
<body>

<div class="container">
 <?php include_once('nav.php'); ?>

 <div class="jumbotron">
 <?php  if(isset($msg)) echo "<h3 style='text-align:center;color:red'>".$msg."</h3>"; ?>
<form class="form-horizontal" role="form" action="" method="POST">
<div class="form-group">
    <label class="col-sm-2 control-label"></label>
    <div class="col-sm-10">
      <p class="form-control-static">Start</p>
    </div>
</div>
  <div class="form-group">
    <label for="inputPassword" class="col-sm-2 control-label">http://haraj.com.sa/</label>
    <div class="col-sm-10">
      <input type="text" name="start" class="form-control" id="inputPassword" placeholder="<?php echo (isset($lstart))? $lstart : '';?>">
    </div>
  </div>
  
 <div class="form-group">
    <label class="col-sm-2 control-label"></label>
    <div class="col-sm-10">
      <p class="form-control-static">End</p>
    </div>
</div>
  <div class="form-group">
    <label for="inputPassword" class="col-sm-2 control-label">http://haraj.com.sa/</label>
    <div class="col-sm-10">
      <input type="text" name="end" class="form-control" id="inputPassword" placeholder="<?php echo (isset($fend))? $fend : '';?>">
	  <span class="label label-success"><?php echo (isset($lend))? $lend : '';?></span>
    </div>
  </div>
   <div class="form-group" style="direction:rtl;text-align:right;font-family:tahoma">
			<br/><input type="checkbox" name="twitterON" value='ON'> نشر بتويتر 
			<br/><input type="checkbox"  name="smsOFF" value='ON'> لا ترسل رسالة نصيه 
			<br/><input type="checkbox"  name="smsON" value='OFF'> لا ترسل رسالة نصيه للجوال المرسل له قبل ذلك
			<br/><input type="checkbox"  name="SendTo36" value='ON'>  أرسل لـ    3rd6lb.com  الآن 
			<br/>
			 حدد وقت الإرسال 
			<input type="text"  class="span2" name="smsTime" value="0" placeholder='hh:mm:ss'>
			<span class="help-inline">hh:mm:ss</span>
    </div>
	  
	<button type="submit" class="btn btn-default">Submit</button>
	
</form>

...</div>
</div>

</body>
</html>
