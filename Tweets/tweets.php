<?php
require_once("../LIB/LIB_http.php");
require_once("../LIB/LIB_parse.php");
require_once("config.php");
$data = array('username'=>'admin','password'=>'admin','remember'=>'0','login'=>'تسجيل الدخول');
http_post_form("http://www.toptwet.com/re_twitter/login",'Google.com',$data);


for($page= 1; $page < 11; $page++ ){
	$content =  http_get("http://www.toptwet.com/re_twitter/library?cid=8&page=".$page, "Google.com");
	//$matches = return_between($content['FILE'], "<tbody>", "</tbody>",EXCL);
	$matches = parse_array($content['FILE'], "<tr>", "</tr>");
	for($i= 1; $i< 16; $i++ ){
		$tweet = trim(strip_tags($matches[$i]));
		$query = $mysqli->query("INSERT INTO `library` (tweet,category_id) VALUES ('$tweet',8 )");
	}	
}

	exit;
		

if(isset($_POST['start']) && isset($_POST['end'])){
	$start = (int)$_POST['start'];
	$end = (int) $_POST['end'];
	
	for($cat= 1; $cat<= $start; $cat++ ){
	
	
		// @preg_match('/<title>(.*)<\/title>/i', $contents, $title); // 	echo "<xmp>".@$title[1]."</xmp>";
		// $category = @explode("|",$title[1]);   //echo "<xmp>".@$category[1]."</xmp>";
		// @preg_match('/<b><a href="http:\/\/haraj.com.sa\/city\/(.*)<\/a>/i', $contents, $city); // City
		
		
		 // if(!empty($matches[1]) && strlen($matches[1]) > 5 ){
			// $number = getNumber($matches[1]);
			// $querystring = $start." - ".$i." - ".$end;
			// if(empty($category[1]))
				// $category[1] = "مطلوب";
			// if(!empty($number))
				// InsertData($mysqli,$number,"haraj",$querystring,$category[1],strip_tags($city[0]));
		// 
	}
	$msg = "Processing finished ..................";
}

//==================== Last Process ========================== //
	
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
<!-- Latest compiled and minified JavaScript -->
<script src="http://netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
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
<!-- Static navbar -->
      <div class="navbar navbar-default" role="navigation">
        <div class="navbar-header">
          <a class="navbar-brand" href="#"></a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li class="active"><a href="haraj.php">حراج </a></li>
            <li><a href="mstaml.php">مستعمل </a></li>
			 <li><a href="./">الرئيسية </a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>

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
    <label for="inputPassword" class="col-sm-2 control-label">Categoris </label>
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
    <label for="inputPassword" class="col-sm-2 control-label">Pages</label>
    <div class="col-sm-10">
      <input type="text" name="end" class="form-control" id="inputPassword" placeholder="<?php echo (isset($fend))? $fend : '';?>">
	  <span class="label label-success"><?php echo (isset($lend))? $lend : '';?></span>
    </div>
  </div>
  
	<button type="submit" class="btn btn-default">Submit</button>
	
</form>

...</div>
</div>

</body>
</html>
