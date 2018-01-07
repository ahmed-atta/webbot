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
</head>
<body>
    <div class="container">

     <?php include_once('nav.php'); ?>

      <!-- Main component for a primary marketing message or call to action -->
      <div class="jumbotron">
		<form class="form-inline" role="form" method="POST" action="">
		  <div class="form-group">
			<input  type="text" name="from" class="form-control" id="exampleInput2" placeholder="From: 2013-09-01">
		  </div>
		  <div class="form-group"> 
			<input type="text" class="form-control" id="exampleInput1" name="to" placeholder="To: 2013-09-21">
		  </div>
		  <div class="checkbox">
			<label> 
			  <input type="checkbox" name="All" value="checked" <?php echo (isset($_POST["All"]))? $_POST["All"]:'checked'; ?> >  All  &nbsp;&nbsp;&nbsp;
			</label>
		  </div>
		    <div class="form-group"> 
				<select style="height: 150px;" class="form-control" name='categories[]' multiple>
				  <option value='-1'>========= كل الأقسام ========</option>
				  <?php   require_once("config.php");
					  $rs_category = $mysqli->query("SELECT DISTINCT category FROM `phones` ;");
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
			</div>  
			<div class="form-group"> 
				<select style="height: 150px;" class="form-control" name='cities[]' multiple >
				  <option value='-1'>========= كل المدن ========</option>
				  <?php   require_once("config.php");
					  $rs_city = $mysqli->query("SELECT DISTINCT city FROM `phones` ;");
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
			</div>  
			
		  <input type="hidden" class="form-control" id="setInput" name="submit" value="set">
		  <button type="submit" class="btn btn-default"  >  &nbsp;&nbsp GO  &nbsp;&nbsp </button>
		</form>
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
			if(isset($_POST["All"]) && $_POST["All"] == "checked") {
					$category_coditions = setCondition('WHERE ');
						$result =$mysqli->query("SELECT * FROM `phones` ".$category_coditions) or die(mysql_error());
					if (mysqli_num_rows($result) > 0 ) {
							
							while($row = $result->fetch_row()){
								$rows.= strip_tags($row[1]).",";
							}
					} else 
						$rows .="NO RESULTS";
			}else {
				$from = $_POST['from'];
				$to = $_POST['to'];
				$category_coditions = setCondition('AND ');
				$result =$mysqli->query("SELECT * FROM `phones` WHERE created > '$from' AND created < '$to' ".$category_coditions) or die(mysql_error());
				if (mysqli_num_rows($result) > 0 ) {
							
							while($row = $result->fetch_row()){
								$rows.= strip_tags($row[1]).",";
							}
				} else 
						$rows .="NO RESULTS";	 
				
			}
		}	
			
		?>
		<textarea class="form-control" rows="20"><?php echo $rows; ?></textarea>
		
      </div>

    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>


</body>
</html>
