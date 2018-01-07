<?php

require_once("config.php");
//============== GET ACTION ======= //
if(isset($_GET['action'])){
	switch($_GET['type']){
		case 'M' :
		{
			$mysqli->query("DELETE FROM `members` WHERE `pid`= ".$_GET['id'].";");
		} break;
		case 'C' :
		{
			$mysqli->query("DELETE FROM `classes` WHERE `ID`= ".$_GET['id'].";");
		} break;
		case 'CO' :
		{
			$mysqli->query("DELETE FROM `locations` WHERE `id`= ".$_GET['id'].";");
		} break;
		case 'MO' :
		{
			$mysqli->query("DELETE FROM `blacklist` WHERE `id`= ".$_GET['id'].";");
		} break;
		case 'HMC' :
		{
			$mysqli->query("DELETE FROM `hm_classes` WHERE `id`= ".$_GET['id'].";");
		} break;
		default:
		break;
	
	}
	$msg = "Processing finished .................. <meta http-equiv='refresh' content='0;URL=settings.php'>";
	
}

// =========== POST ACTOIN ======= //
if(isset($_POST['action'])){
	switch($_POST['action']){
		case 'M' :
		{
			$mysqli->query("INSERT INTO `members` (`ID`,`name`,`mobile`,`mb_sms`) VALUES (".$_POST['ID'].",'".$_POST['Name']."','".$_POST['Mobile']."','".$_POST['mb_sms']."');");
		} break;
		case 'C' :
		{
			$categories = '';
			foreach($_POST["categories"] as $qk=>$qca){
				if($qca != -1){
					$categories .= $qca ."#";
				}
			}
			//print_r($_POST["categories"]);
			//echo "<br/>".$categories;
			$categories = rtrim($categories,'#');
			//echo "<br/>".$categories;
			//exit;
			$mysqli->query("INSERT INTO `classes` (`ID`,`name`,`member_ID`,`m_class`,`SellType`) VALUES (".$_POST['ID'].",'".$_POST['Name']."',".$_POST['member'].",'$categories','".$_POST['SellType']."');");
		} break;
		case 'CO' :
		{
			$locations = '';
			foreach($_POST["cities"] as $ck=>$cci){
				if($cci != -1){
					$locations .= $cci."#";			 
				} 
			}
			$locations = rtrim($locations,'#');
			$mysqli->query("INSERT INTO `locations` (`country_ID`,`country_name`,`city_ID`,`city_name`,`m_location`) VALUES (".$_POST['country_ID'].",'".$_POST['country_Name']."',".$_POST['city_ID'].",'".$_POST['city_Name']."','$locations');");
		} break;
		case 'MO' :
		{
			$mysqli->query("INSERT INTO `blacklist` (`mobile`) VALUES ('".$_POST['mobile']."');");
		} break;
		case 'HMC' :
		{
			$mysqli->query("INSERT INTO `hm_classes` (`name`) VALUES ('".$_POST['name']."');");
		}
		default:
		break;
	
	}
	$msg = "Processing finished ..................";
}

//==================== Last Process ========================== //
 if ($rs_p = $mysqli->query("SELECT querystring FROM `phones` WHERE site = 'haraj' ORDER BY id DESC LIMIT 1;")){
	//mysqli_num_rows($rs_p) > 0 ) {  
		//$row = $rs_p->fetch_assoc();
	//}
	
 
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
<script src="http://getbootstrap.com/2.3.2/assets/js/jquery.js"></script>
<script src="http://getbootstrap.com/2.3.2/assets/js/bootstrap-tab.js"></script>
<style>
.jumbotron{
	padding-top: 5px;
	font-size:12px;
	
}
.jumbotron form input[type='text']{
direction : rtl;
}

</style>
</head>
<body>

<div class="container">
 <?php include_once('nav.php'); ?>

 <div >
 <?php  if(isset($msg)) echo "<h3 style='text-align:center;color:red'>".$msg."</h3>"; ?>
 
 <div class="tabbable" style="float:right;direction:rtl"> <!-- Only required for left/right tabs -->
  <ul class="nav nav-tabs">
    <li class="active"><a href="#tab1" data-toggle="tab">الأعضاء</a></li>
	 <li><a href="#tab6" data-toggle="tab">تصنيفات حراج/مستعمل</a></li>
    <li><a href="#tab2" data-toggle="tab">التصنيفات</a></li>
	<li><a href="#tab3" data-toggle="tab">المكان </a></li>
	<li><a href="#tab5" data-toggle="tab">الجوال المحظور</a></li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane active" id="tab1">
			 <h5>=============== الأعضاء ==============</h5>
		<form class="form-horizontal" role="form" action="" method="POST">
		  <div class="form-group">
			<label>ID :</label>
			<input type="text" name="ID"	placeholder="ID">
			<label>الإسم :</label>
			<input type="text" name="Name"  placeholder="Name">
			<label>الجوال :</label>
			<input type="text" name="Mobile"  placeholder="Mobile">
			
		  </div>
		   <div class="form-group">
		   <label>نص الرساله :</label>
			<textarea name="mb_sms" cols='80' rows='5' ></textarea>
		   </div>
			<input type="hidden" name="action" class="form-control" value="M">
			<button type="submit" class="btn btn-default">أضف</button>
		</form>
		<br/>
		<table class="table table-bordered">
		 <thead>
		  <th>ID</th>
		  <th>اسم العضو</th>
		  <th>الجوال</th>
		   <th>نص الرساله </th>
		 </thead>
		<tbody>
		<?php 
		if ($members = $mysqli->query("SELECT * FROM `members` ORDER BY id DESC ")){
			if(mysqli_num_rows($members) > 0 ) {  
				while($member = $members->fetch_assoc()){
					echo "<tr><td>".$member['ID']."</td><td>".$member['name'].
					"</td><td>".$member['mobile'].
					"</td><td>".$member['mb_sms'].
					"</td><td><a href='?action=del&type=M&id=".$member['pid']."' class='btn btn-danger' >حذف</a></td></tr>";
				}
			}
		}
		?>
		</tbody>
		</table>
		<hr/>
	 
	 
    </div>
    <div class="tab-pane" id="tab2">
			 
		<h5>=============== التصنيفات ==============</h5>
		<form class="form-horizontal" role="form" action="" method="POST">
		  <div class="form-group">
			<label>ID :</label>
			<input type="text" name="ID"	placeholder="ID">
			<label>التصنيف :</label>
			<input type="text" name="Name"  placeholder="Name">
			<label>النوع :</label>
			<select name='SellType' >
				<option value="1"> عرض </option>
				<option value="2"> طلب </option>
			</select>
			<label>الأعضاء</label>
			<select name='member' >
				  <?php  
					  $rs_members = $mysqli->query("SELECT * FROM `members` ;");
						if (mysqli_num_rows($rs_members) > 0 ) {  
							while($row_member = $rs_members->fetch_assoc()){
								echo "<option value='".$row_member['pid']."' > ".$row_member['name']." </option>";
							}
						}
				?>
			</select>
			</div>
		    <div class="form-group"> 
				<select style='height:300px' name='categories[]' multiple>
				  <option value='-1'>========= كل الأقسام ========</option>
				   <option value=' '>---</option>
				  <?php 
					  $rs_category = $mysqli->query("SELECT name FROM `hm_classes` ORDER BY name ASC;");
						if (mysqli_num_rows($rs_category) > 0 ) {  
							while($row = $rs_category->fetch_assoc()){
								$cat = $row['name'];
								echo "<option value='$cat'> $cat </option>";
							}
						}
				?>
				</select>
			</div>  
			<input type="hidden" name="action" class="form-control" value="C">
			<button type="submit" class="btn btn-default">أضف </button>
		</form>
		<br/>
		<table class="table table-bordered">
		 <thead>
		  <th>ID</th>
		  <th>اسم التصنيف</th>
		  <th>العضو ID</th>
		   <th>نوع الإعلان</th>
		   <th>التصنيفات المختاره</th>
		 </thead>
		<tbody>
		<?php 
		if ($classes = $mysqli->query("SELECT * FROM `classes` ORDER BY ID DESC ")){
			if(mysqli_num_rows($classes) > 0 ) {  
				while($class = $classes->fetch_assoc()){
					$m_class = str_replace('#',"<br/>",$class['m_class']); 
					echo "<tr><td>".$class['ID'].
					"</td><td>".$class['name'].
					"</td><td>".$class['member_ID'].
					"</td><td>".$class['SellType'].
					"</td><td>".$m_class."</td>
					<td><a href='?action=del&type=C&id=".$class['ID']."' class='btn btn-danger' >حذف</a></td></tr>";
				}
			}
		}
		?>
		</tbody>
		</table>
		<hr/>
	 
    </div>
	 <div class="tab-pane" id="tab3">
			 <h5>=============== المكان ==============</h5>
		<form class="form-horizontal" role="form" action="" method="POST">
		  <div class="form-group">
			<label>الدوله ID :</label>
			<input type="text" name="country_ID"	placeholder="ID">
			<label>الدولة :</label>
			<input type="text" name="country_Name"  placeholder="Name">
		  </div>
		   <div class="form-group">
			<label>المدينه ID :</label>
			<input type="text" name="city_ID"	placeholder="ID">
			<label>المدينة :</label>
			<input type="text" name="city_Name"  placeholder="Name">
		  </div>
		  <div class="form-group"> 
				<select style="height: 300px;" class="form-control" name='cities[]' multiple >
				  <option value='-1'>========= كل المدن ========</option>
				  <?php 
					  $rs_city = $mysqli->query("SELECT DISTINCT city FROM `hm_cities` ORDER BY city ASC;");
						if (mysqli_num_rows($rs_city) > 0 ) {  
							while($row_city = $rs_city->fetch_assoc()){
								$city = $row_city['city'];
								echo "<option value='$city' > $city </option>";
							}
						}
				?>
				</select>
			</div>  
		  
			<input type="hidden" name="action" class="form-control" value="CO">
			<button type="submit" class="btn btn-default">Add</button>
		</form>
		<br/>
		<table class="table table-bordered">
		 <thead>
		  <th>الدولة ID</th>
		  <th>اسم الدوله</th>
		   <th>المدينه ID</th>
		    <th>اسم المدينه </th>
			 <th>المكان </th>
		 </thead>
		<tbody>
		<?php 
		if ($countries = $mysqli->query("SELECT * FROM `locations` ORDER BY id DESC")){
			if(mysqli_num_rows($countries) > 0 ) {  
				while($country = $countries->fetch_assoc()){
					$m_location = str_replace('#',"<br/>",$country['m_location']); 
					echo "<tr><td>".$country['country_ID']."</td><td>".$country['country_name']."</td><td>".
					$country['city_ID']."</td><td>".$country['city_name']."</td><td>".$m_location.
					"</td><td><a href='?action=del&type=CO&id=".$country['id']."' class='btn btn-danger' >حذف</a></td></tr>";
				}
			}
		}
		?>
		</tbody>
		</table>
		<hr/>
	 </div>
	  <div class="tab-pane" id="tab5">
	 
		 <h5>=============== أرقام الجوال المحظوره ==============</h5>
		<form class="form-inline" role="form" action="" method="POST">
		  <div class="form-group">
			<label>الجوال :</label>
			<input type="text" name="mobile"  placeholder="mobile">
		  </div>
			<input type="hidden" name="action" class="form-control" value="MO">
			<button type="submit" class="btn btn-default">Add</button>
		</form>
		<br/>
		<table class="table table-bordered">
		 <thead>
		  <th>الرقم</th>
		 </thead>
		<tbody>
		<?php 
		if ($mobiles = $mysqli->query("SELECT * FROM `blacklist` ORDER BY id DESC ")){
			if(mysqli_num_rows($mobiles) > 0 ) {  
				while($mobil = $mobiles->fetch_assoc()){
					echo "<tr><td>".$mobil['mobile']."</td><td><a href='?action=del&type=MO&id=".$mobil['id']."' class='btn btn-danger' >حذف</a></td></tr>";
				}
			}
		}
		?>
		</tbody>
		</table>
		<hr/>
	 
	 
	 </div>
	  <div class="tab-pane" id="tab6">
	 <h5>=============== تصنيفات حراج / مستعمل ==============</h5>
		<form class="form-inline" role="form" action="" method="POST">
		  <div class="form-group">
			<label>اسم التصنيف :</label>
			<input type="text" name="name"  placeholder="">
		  </div>
			<input type="hidden" name="action" class="form-control" value="HMC">
			<button type="submit" class="btn btn-default">أضف </button>
		</form>
		<br/>
		<table class="table table-bordered">
		 <thead>
		  <th>التصنيف </th>
		 </thead>
		<tbody>
		<?php 
		if ($hmc = $mysqli->query("SELECT * FROM `hm_classes` ORDER BY id DESC ")){
			if(mysqli_num_rows($hmc) > 0 ) {  
				while($h = $hmc->fetch_assoc()){
					echo "<tr><td>".$h['name']."</td><td><a href='?action=del&type=HMC&id=".$h['id']."' class='btn btn-danger' >حذف</a></td></tr>";
				}
			}
		}
		?>
		</tbody>
		</table>
		<hr/>
	 
	 
	 <div>
	  <div class="tab-pane" id="tab7">
	 
	 <div>
	
	
  </div>
</div>
 
 
 
 
 
 
</div>
</div>

</body>
</html>
