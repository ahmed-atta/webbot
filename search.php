<?php
if(isset($_POST['submit'])) {
   	if($_POST['bot_type'] == "Request"){
		include('webbot.class.php');
		$wb  = new webbot('');
		$search['action'] =  "http://www.bing.com/search";//"http://www.google.com/search";
		$search['q']= array('q' => $_GET['q']);
		$search['method'] = "GET";	
		$search['xpath'] = "//*[@id='results']";// "//*[@id='ires']";
		
		//$report = $wb->analysisURL();
		//echo $report;
		$result = $wb->request($search['action'],$search['q'],$search['method'],$search['xpath']);
		
		echo "<xmp>".$result. "</xmp><br />";
		
	} else if($_POST['bot_type'] == "Response"){
		require_once("LIB/LIB_http.php");
		require_once("LIB/LIB_parse.php");
		
		$result = http_get($_POST['url'], "parmagy.com");
		if($result['HTTP_CODE'] == 200){
			$html = $result['FILE'];
			$link= queryXPath($html,"//div");
			
			echo "<xmp>".$link. "</xmp><br />";
		} else {
			print_r($result['STATUS']);
			print_r($result['ERROR']);
		}
		
		//echo $result['HTTP_CODE'];
		
	} 
	
	//$title = $wb->get_between($url,"<title>","</title>",1);
	//$body =  $wb->get_between($url,"<body ","</body>",1);
	//$meta = $wb->get_elements($url,"<div", ">");
	// $id = "node-2583"; 
	// $content = $wb->get_page($url);
	// $pre_content = $wb->remove_tag($content,"<fb","</fb");
	// echo "<xmp>".$pre_content."</xmp>";
	// $wb->getHTML_errors($pre_content);

	// $cleaned_html = tidy_html($pre_content);
	// $innerHtml = @$wb->get_innerHTML($cleaned_html,$id);
	
	
	//=======================  END check	
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title>PARMAGY.COM WEB BOT</title>
  </head>
  <body>
		<form name="searchForm" id="searchForm" method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>">
		     <table border="0">
			<tr>
			<td>URL :</td>
			<td><input type="text" name="url"  id="url" maxlength="256" size="100"/></td>
			</tr>
			<tr>
			<td>Type</td>
			<td><select name='bot_type'>
				<option value="Request">Request</option>
				<option value="Response">Response</option>
			</select></td>
			</tr>
			<tr>
			<td>Request Type:</td>
			<td>
				<select name='request_type'>
					<option value="GET">GET</option>
					<option value="POST">POST</option>
				</select>
			</td>
			</tr>
			<tr>
			<td>Request Data:</td>
				<td> <textarea name="request_data" rows="10" cols="50"></textarea></td>
			</tr>
			<tr>
			<td></td>
			<td> <input type="submit" name="submit" id="submit" value="Send"/></td>
			</tr>
			</table>
			
        </form>
  
  </body>
  </html>
 