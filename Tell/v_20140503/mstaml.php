<?php
require_once("config.php");
if(isset($_POST['start']) && isset($_POST['end'])){

	$start = (int)$_POST['start'];
	$end = (int) $_POST['end'];
	
	for($i = $start; $i<= $end; $i++ ){
		$contents = @file_get_contents("http://www.mstaml.com/".$i,FILE_TEXT,$context);
		@preg_match('/userInfo(.*)(;)/i', $contents, $matches);
		@preg_match('/<b>القسم<\/b>(.*)<\/a>/i', $contents, $category); // Category 
		@preg_match('/<b>المكان<\/b>(.*)<\/a>/i', $contents, $city); // City
		//@preg_match("/<div class=\"boxDarkBody\s mb0\">(.*)<div class=\"boxItem\s mb30\">/si", $contents, $ad_text); // Ad_text
		@preg_match("(<div class=\"titleSection doHighlight\">(.*)</div>)siU",$contents, $ad_title);
		@preg_match("(<div class=\"text linkify linkifyWithImages linkifyWithWasel doHighlight\">(.*)</div>)siU",$contents, $ad_text);
		@preg_match("(<a href=\"#user\">(.*)</a>)siU",$contents, $ad_user);
		//==================== Images
		@preg_match("(<noscript>(.*)</noscript>)siU",$contents, $images);
		@preg_match_all("(src=\"(.*)\")siU",$images[1],$srcs);
		$imgs_str = '';
		if(!empty($srcs[1])){
			$imgs_str = implode("#",$srcs[1]);
		}
		
		$categ = @mb_convert_encoding($category[1], 'UTF-8',mb_detect_encoding($category[1], 'UTF-8, ISO-8859-1', true));
		$city_c = @mb_convert_encoding($city[1], 'UTF-8',mb_detect_encoding($city[1], 'UTF-8, ISO-8859-1', true));
		$categss  = @strip_tags($categ); // echo  "<xmp>".$categ."</xmp>";
		$categ = explode("-",$categss);
		$categ = trim($categ[0],":");
		$city_c = trim($city_c,":");
		
		$city_c  = @strip_tags($city_c); // echo  "<xmp>".$categ."</xmp>";
		$ads_title = $ad_title[1];
		$ads_text = strip_tags($ad_text[0]);
		$ads_user = $ad_user[1];
		
		//============================================//
		$data =  @explode(",",$matches[0]);
		$mobile = @explode(":",$data[1]);
		$phone =  @explode(":",$data[2]);
		$other = @explode(":",$data[3]);
		
		
		$m = @trim(str_replace('"', " ", $mobile[1]));
		$p = @trim(str_replace('"', " ", $phone[1]));
		$o = @trim(str_replace('"', " ",rtrim( $other[1],"};")));

		 $Tels = array();
		if(!empty($m) && strlen($m) > 5){
			 $Tels[$m] = 1;
		}
		if(!empty($p) && strlen($p) > 5 ){
			 $Tels[$p] = 1 ;
		}
		if(!empty($o) && strlen($o) > 5 ){
			 $Tels[$o] = 1;
		}
		foreach( $Tels as $key=>$value){
		    //echo $key."<br/>";
			$number = getNumber($key);
			$querystring = $start." - ".$i." - ".$end;
			$ad_ID = $i;
			$ad_exist_flag = false ;
			$ad_exist = $mysqli->query("SELECT id FROM `ads` WHERE `ad_ID`='$ad_ID';");
			if($ad_exist->num_rows > 0){
				$ad_exist_flag  = 1;
			}
			if(!empty($number)&& !$ad_exist_flag ){
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
				InsertData($mysqli,$number,"mstaml",$querystring,$categ,$city_c,$ad_ID,strip_tags($ads_text),strip_tags($ads_title),$ads_user,$twitterON,$sms);
				
				//============================== INSRT INTO adds
				$m_class = trim($categ);
				$m_location = trim($city_c);
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
				if(isset($class['ID']) && isset($class['MID']) 
				&& isset($location['country_ID'])
				&& isset($location['city_ID']) ){
					
					if(isset($_POST['SendTo36']) && $_POST['SendTo36'] =='ON'){
							$arrayData['Name']= strip_tags($ads_title);
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
							$arrayData['mb_smsTime'] = $_POST['smsTime'];
							$reqs = http("http://3rd6lb.com/_R1.php","google.com","POST",$arrayData ,false);
							
						} else {
							$mysqli->query("INSERT INTO `adds` (`Name`,
							`Details`,`MemberID`,`ClassID`,`CountryID`,
							`CityID`,`Type`,`SellType`,`Active`,`Params`,`Album`,`twitter`,`sms`,`m_mobile`,`m_name`,`mb_sms`,`smsTime`) 
											VALUES ('".strip_tags($ads_title)."','".
														strip_tags($ads_text)."',".
														$class['MID'].",".
														$class['csID'].",".
														$location['country_ID'].",".
														$location['city_ID'].",2,
														".$class['SellType'].",1,'$params','$imgs_str',$twitterON,$sms,'".
														$class['mobile']."','".$class['name']."','".
														$class['mb_sms']."','".
														$_POST['smsTime']."');");
						}
				}
			}
		}
	}
	$msg = "Processing finished ..................";
}


//==================== Last Process ========================== //
$result = $mysqli->query("SELECT querystring FROM `ads` WHERE site = 'mstaml' ORDER BY id DESC LIMIT 1;");
    if (mysqli_num_rows($result) > 0 ) {  
		$row = $result->fetch_assoc();
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
    <label for="inputPassword" class="col-sm-2 control-label">http://www.mstaml.com/</label>
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
    <label for="inputPassword" class="col-sm-2 control-label">http://www.mstaml.com/</label>
    <div class="col-sm-10"> 
      <input type="text" name="end" class="form-control" id="inputPassword" placeholder="<?php echo (isset($fend))? $fend : '';?>">
	  <span class="label label-success"><?php echo (isset($lend))? $lend : '';?></span>
    </div>
  </div>
   <div class="form-group" style="direction:rtl;text-align:right;font-family:tahoma">
			<br/><input type="checkbox" name="twitterON" value='ON'> نشر بتويتر 
			<br/><input type="checkbox"  name="smsOFF" value='ON'> لا ترسل رسالة نصيه 
			<br/><input type="checkbox"  name="smsON" value='OFF'>  لا ترسل رسالة نصيه للجوال المرسل له قبل ذلك
			<!--br/><input type="checkbox" name="adsON" value='OFF'>  لا تنسخ إعلانات الجوال المرسل له قبل ذلك  <-->
			<br/><input type="checkbox"  name="SendTo36" value='ON'>  أرسل لـ    3rd6lb.com  الآن
			<br/>
			 حدد وقت الإرسال 
			<input type="text"  name="smsTime" value="0" placeholder='hh:mm:ss'>
			<span class='color:#eee'>hh:mm:ss</span>
    </div>
	  
	<button type="submit" class="btn btn-default">Submit</button>
	
</form>

</div>
</div>

</body>
</html>
