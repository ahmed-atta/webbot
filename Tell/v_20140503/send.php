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
		<form class="form-inline" role="form" method="POST" action="">
		    <!--div class="form-group"> 
				<select style="height: 150px;" class="form-control" name='categories[]' multiple>
				  <option value='-1'>========= كل الأقسام ========</option>
				  <?php   require_once("config.php");
					  $rs_category = $mysqli->query("SELECT DISTINCT category FROM `ads` ;");
						if (mysqli_num_rows($rs_category) > 0 ) {  
							while($row = $rs_category->fetch_assoc()){
								$cat = $row['category'];
								if(isset($_POST["categories"])){
									if(in_array($cat,$_POST["categories"]))
										echo "<option value='$cat' selected> $cat </option>";
									else  
										echo "<option value='$cat'> $cat </option>";
								} else 
									echo "<option value='$cat'> $cat </option>";
							}
						}
				?>
				</select>
			</div -->  
			<!-- div class="form-group"> 
				<select style="height: 150px;" class="form-control" name='cities[]' multiple >
				  <option value='-1'>========= كل المدن ========</option>
				  <?php  
					  $rs_city = $mysqli->query("SELECT DISTINCT city FROM `ads` ;");
						if (mysqli_num_rows($rs_city) > 0 ) {  
							while($row_city = $rs_city->fetch_assoc()){
								$city = $row_city['city'];
								if(isset($_POST["cities"])){
									if(in_array($city,$_POST["cities"])){
										echo "<option value='$city' selected> $city </option>";
									} else 
										echo "<option value='$city' > $city </option>";
								} else
									echo "<option value='$city' > $city </option>";
							}
						}
				?>
				</select>
			</div --> 
			<!-- div> ==============================  TO  ==========================</div -->
			<br/>
			<div class="form-group"> 
				<label>الأعضاء</label>
				<select  class="form-control" name='member' >
				  <?php  
					  $rs_members = $mysqli->query("SELECT * FROM `members` ;");
						if (mysqli_num_rows($rs_members) > 0 ) {  
							while($row_member = $rs_members->fetch_assoc()){
								echo "<option value='".$row_member['ID']."' > ".$row_member['name']." </option>";
							}
						}
				?>
				</select>
			</div>  
			<div class="form-group"> 
				<label>التصنيف </label>
				<select  class="form-control" name='class' >
				  <?php  
					  $rs_classes = $mysqli->query("SELECT * FROM `classes` ;");
						if (mysqli_num_rows($rs_classes) > 0 ) {  
							while($row_class = $rs_classes->fetch_assoc()){
								echo "<option value='".$row_class['ID']."' > ".$row_class['name']." </option>";
							}
						}
				?>
				</select>
			</div>  
			<div class="form-group"> 
				<label>الدولة  </label>
				<select  class="form-control" name='country' >
				  <?php  
					  $rs_countries = $mysqli->query("SELECT * FROM `locations` ;");
						if (mysqli_num_rows($rs_countries) > 0 ) {  
							while($row_country = $rs_countries->fetch_assoc()){
								echo "<option value='".$row_country['id']."' > ".$row_country['country_name']." -- ".$row_country['city_name']." </option>";
							}
						}
				?>
				</select>
			</div>
			  <input type="checkbox" name="block" value="checked" > استبعاد اعلانات الجوال المحظوره &nbsp;&nbsp;&nbsp;

			
		  <input type="hidden" class="form-control" id="setInput" name="submit" value="set">
		  <button type="submit" class="btn btn-default"  >  &nbsp;&nbsp GO  &nbsp;&nbsp </button>
		</form>
		<hr/>
		<br/>
		<!-- =============================== -->
<?php $rows ='';
		
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
					if(sizeof($_POST["cities"]) >1)
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
	if(isset($_POST['submit'])){
		//=================================================================//
		// $category_coditions = setCondition('WHERE ');
		// if(isset($_POST["block"]) && $_POST["block"] == "checked"){
			// $re_blacklist = $mysqli->query("SELECT mobile FROM `blacklist`;");
			// $lists = '';
			// while($list = $re_blacklist->fetch_assoc()){
				// $lists .= "'".$list['mobile']."',";
			// }
				// $lists = rtrim($lists,",");
			// if($lists !='')
				// $category_coditions .=" AND(phone NOT IN(".$lists."))";
		// }
			// if($result =$mysqli->query("SELECT * FROM `ads` ".$category_coditions)) {
					// if (mysqli_num_rows($result) > 0 ) {
						// while($row = $result->fetch_assoc()){
								// $mysqli->query("INSERT INTO `adds` (`Name`,`Details`,`MemberID`,`ClassID`,`CountryID`,`CityID`,`SellType`) 
								// VALUES ('".$row['ad_title']."','".
											// $row['ad_text']."',".
											// $_POST['member'].",".
											// $_POST['class'].",".
											// $_POST['country'].",".
											// $_POST['city'].",1);");
								
								
							// }	
					// } 
					////echo "</tbody></table>";
			// } else {
			// }
	}	
	
	if($result =$mysqli->query("SELECT * FROM `adds`")) {
		echo "<table class='table table-bordered'>
								 <thead>
									  <th>العنوان</th>
									  <th>العضو</th>
									  <th>التصنيف</th>
									  <th>الدولة</th>
									  <th>المدينه</th>
									  
								 </thead>
								<tbody>";
					if (mysqli_num_rows($result) > 0 ) {
							while($row = $result->fetch_assoc()){
							   
								//$reqs = http("http://localhost/_demos/3rd6lb.com/_R1.php","google.com","POST",$row ,false);
								echo"<tr><td><a href='ad_v.php?id=".$row['ID']."'>".$row['Name']."</a></td><td>".
								$row['MemberID']."</td><td>".
								$row['ClassID']."</td><td>".
								$row['CountryID']."</td><td>".
								$row['CityID']."</td></tr>".
								"<tr><td colspan='5'>";
								//print_r($reqs['FILE']);
								echo "</td></tr>";
								
								
								
							}
					} else 
						$rows .="NO RESULTS";
					echo "</tbody></table>";	
	
	}
		?>
      </div>
    </div> <!-- /container -->
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>


</body>
</html>
