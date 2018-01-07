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
	padding-top: 5px;
	font-size:12px;
	
}
.jumbotron form input[type='text'],table{
	direction : rtl;
	
}
table th {
	text-align:right;
	background:#ccc;
}
</style>
</head>
<body>
    <div class="container">
     <?php include_once('nav.php'); ?>
      <!-- Main component for a primary marketing message or call to action -->
      <div class="jumbotron">
		<!-- =============================== -->
<?php 
require_once('config.php');
$rows ='';
		
function setCondition($params){
	$category_coditions = '';
	if(isset($_POST["categories"])){
		foreach($_POST["categories"] as $qk=>$qca){
			if($qca == -1){
				$category_coditions = '';
				break;					 
			} else {
				$category_coditions .=" category LIKE '%".$qca."%' ";
					if(sizeof($_POST["categories"]) >1)
						$category_coditions .=" OR";
			}						
		}
	}
	//=========================== Cities ============== //
	$city_coditions  ='';
	if(isset($_POST["cities"])){
		foreach($_POST["cities"] as $ck=>$cci){
			if($cci == -1){
				$city_coditions = '';
				break;					 
			} else {
				$city_coditions .=" city LIKE '%".$cci."%' ";
					if(sizeof($_POST["cities"]) > 1)
						$city_coditions .=" OR";
			}						
		}
	}
	//echo rtrim($category_coditions,"OR");
	if(!empty($category_coditions) && !empty($city_coditions)){
		$coditions = $params ."(".rtrim($category_coditions,'OR').") AND (".rtrim($city_coditions,'OR').")";
	}else if (!empty($category_coditions) && empty($city_coditions)){
		$coditions =  $params . rtrim($category_coditions,'OR');
	}else if (empty($category_coditions) && !empty($city_coditions)){
		$coditions = $params . rtrim($city_coditions,'OR');
	}
	return @$coditions;

}
//=================================================================//
if(isset($_POST['submit'])){
		foreach($_POST['IDs'] as $k =>$ID){
			$rs =$mysqli->query("SELECT * FROM `adds` WHERE `id`= ".$ID);
			$row = $rs->fetch_assoc();
			$reqs = http("http://3rd6lb.com/_R1.php","google.com","POST",$row ,false);
			$mysqli->query("DELETE FROM `adds` WHERE `id`= ".$ID.";");
		}
}	
if(isset($_GET['id'])){
	$mysqli->query("DELETE FROM `adds` WHERE `id`= ".$_GET['id'].";");
}	

	if($result =$mysqli->query("SELECT * FROM `adds`")) {
		echo "<form name='ads' action='".$_SERVER['PHP_SELF']."' method='post'>
		<input type='hidden' name='submit' value='1' />
		<button type='submit' class='btn btn-success' style='float:right'> أرسل لــ....</button> <br/><br/>
		<table class='table table-bordered'>
								 <thead>
									  <th><input type='checkbox' value='' onClick='javascript: return toggle(this);' name='checkAll'></th>
									 <th>العنوان</th>
									  <th>العضو</th>
									  <th>التصنيف</th>
									  <th>الدولة</th>
									  <th>المدينه</th>
									  
								 </thead>
								<tbody>";
					if (mysqli_num_rows($result) > 0 ) {
							while($row = $result->fetch_assoc()){
								echo"<tr>
								<td><input type='checkbox' value='".$row['ID']."' name='IDs[]'></td>
								<td><a href='ad_v.php?id=".$row['ID']."'>".$row['Name']."</a></td><td>".
								$row['MemberID']."</td><td>".
								$row['ClassID']."</td><td>".
								$row['CountryID']."</td><td>".
								$row['CityID']."</td><td><a href='ad_e.php?id=".$row['ID']."' class='btn btn-large btn-primary'>تعديل </a>
								<a href='index.php?id=".$row['ID']."' class='btn btn-large btn-danger'>حذف </a>
								</td></tr>";
								//echo"<tr><td colspan='5'></td></tr>";	
							}
					} else 
						$rows .="NO RESULTS";
					echo "</tbody></table></form>";	
	
	}
		?>
		</div>
      </div>
    </div> <!-- /container -->
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
	<script language="JavaScript">
	 function toggle(source) {
		var myForm = document.forms.ads;
		var checkboxes = myForm.elements['IDs[]'];
		  //checkboxes = document.getElementsByName('ids');
		for(var i=0, n= checkboxes.length; i< n;i++) {
			checkboxes[i].checked = source.checked;
		}
	}
	</script>

</body>
</html>
