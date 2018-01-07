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
	<hr/>
	<form name='ads' action='' method='post'>
	<button type='submit' class='btn btn-success' name='submit' style='float:right'> تعديــل </button> <br/><br/>
	<input type='hidden' name='ID' value="<?php echo $_GET["id"]; ?>" />	
<?php 
require_once("config.php");
if(isset($_POST['submit'])){
	$rs =$mysqli->query("UPDATE `adds` SET  `Name` =  '".$_POST['Name']."',`Details` = '".$_POST['Details']."' WHERE  `ID` =".$_POST['ID']);
	
		
}	
		//=================================================================//
			if(isset($_GET["id"])) {
						$result = $mysqli->query("SELECT * FROM `adds` WHERE ID =".$_GET["id"]) or die(mysql_error());
						
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
							$row = $result->fetch_assoc();
								echo"<tr><td><textarea cols='50' rows='2' name='Name'>".$row['Name']."</textarea></td><td>".
								$row['MemberID']."</td><td>".
								$row['ClassID']."</td><td>".
								$row['CountryID']."</td><td>".
								$row['CityID']."</td></tr><tr><td colspan='5'><textarea cols='150' rows='10' name='Details'>".
								$row['Details']."</textarea></td></tr>";
						
					} else 
						$rows .="NO RESULTS";
					echo "</tbody></table>";
			} 
		?>
		</form>
      </div>
    </div> <!-- /container -->

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
</body>
</html>
