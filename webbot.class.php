<?php
// http://php.net/manual/en/book.curl.php
/**
* @todo Class for creating CURL object and set It's options 
* 
**/
require_once("LIB/LIB_http.php");
require_once("LIB/LIB_parse.php");

//==========================================
class WebBot {
	private $_site = array();
    private $_siteHtml;
	private $_url;
	private $_error;
	private $_ref = '';  
	
	public function __construct($url)
	{
		 $this->_url = $url;
		 # Download a web page = get the html content of web page
		$web_page = http_get($this->_url, $this->_ref); // LIB_http
		$this->_siteHtml = $web_page['FILE'];
		$this->_site['HTTP_CODE'] = $web_page['HTTP_CODE'];
		$this->_site['STATUS'] = $web_page['STATUS'];
	}
	private function _isURL($url) 
	{
		$url_pattern = "/^((https?|ftp):\/\/)?((www)|[a-zA-Z0-9_-]+\.)?[a-z0-9.\-]+[.][a-z]{2,4}/";
		$flag = (bool)preg_match($url_pattern, $url );
		return $flag;
//		if($flag){
//		 	//echo "<xmp>".$this->get_page($url)."</xmp>";
//		 	
//		} else {
//			$result['status']= false ;
//		    $result['message']= "URL is not valid";
//		}
	}
	/**
	 * get report of that url 
	 * @return array stats = ['isUrl','DNS','httpCode','goUrl'/'meta']
	 */
	public function analysisURL()
	{
		$stats= array();
		if($this->_isURL()){
			$stats['isUrl']= true; // set URL is valid
			$p_url = parse_url($this->_url); // parse_url PHP function
			
			$this->_setHttpRequest();
			$stats['DNS'] = $p_url['host'];
			$stats['DNS_R'] =  $this->_site['STATUS']['url'];
			$stats['httpCode']= $this->_site['HTTP_CODE'];  // set HTTP status code
			$stats['title'] = return_between($this->_siteHtml,"<title>","</title>",EXCL); //  LIB_parse
			// META tags reference: http://www.i18nguy.com/markup/metatags.html 
			/*  get html tag and work well with tags [script,link,style,meta,img,a,....] that tags not support nested such as :
		        <div id='1'><div id='2'></div></div>
			*/
			$meta_tags = parse_array($this->_siteHtml, "<meta",">"); // LIB_parse
			foreach($meta_tags as $k => $element){
				$goUrl = $this->isRedirect($element);
				if($goUrl){
					$stats['goUrl'] = $goUrl; // if redirect get that url
					break;
				} else {
					$name = get_attribute($element,'name'); // LIB_parse
					$content = get_attribute($element,'content'); // LIB_parse
					if($name && $content){
						$stats['meta'][$name] = $content;
					}	
				}
			}
		} else {
			$stats['isUrl']= false;
		}
		//return $stats;
		//================  PRINT REPORT
		$report = "<table border='1'>";
		foreach($stats as $k => $val){
			if($k == 'meta'){
				$report .='<tr><td> META </td><td>';
				foreach($val as $key => $value){
					$report .= '<span style="color:red">'.$key.'</span> = '.$value.'<br/>';
				}
				$report .='</td></tr>';
			} else 
			$report .='<tr><td>'.$k.'</td><td>'.$val.'</td></tr>';
		}
		$report .= '</table>';
		return $report;
	}
	/**
	 * 
	 * if meta tag is refresh type <META HTTP-EQUIV="REFRESH" CONTENT="15;URL=http://www.google.com/">
	 * @param $meta_tag
	 */
	function isRedirect($meta_tag){
		$ref =  get_attribute($meta_tag,'http-equiv');  // LIB_parse
			if($ref == 'refresh'){
				 $content = get_attribute($meta_tag,'content');
				 $go_to = split_string($content,'url=',AFTER,EXCL);// LIB_parse
				 return $go_to;
			} else {
				return false;
			}
	}
	
	
	public function remove_tag($content, $start_tag, $end_tag){
		$uncommented_page = remove($content , "<!--", "-->");
		$tag_removed = remove($uncommented_page, $start_tag, $end_tag);
		return $tag_removed;
		//$links_removed = remove($links_removed, "<a", "</a>");
		//$images_removed = remove($content, "<img", " >");
		//$javascript_removed = remove($content, "<script", "</script>");
	}
	public function getHTML_errors($html){
		// $tidy = tidy_parse_string($html);
		// $tidy->cleanRepair();
		//// note the difference between the two outputs
		//// echo $tidy->errorBuffer . "\n";
		// $tidy->diagnose();
		// echo $tidy->errorBuffer;
				
		/* or in OO: */
		//echo $tidy->errorBuffer;
	}
/**	 
 * Extract an element by ID from an HTML document
 * Thanks http://codjng.blogspot.com/2009/10/unicode-problem-when-using-domdocument.html
 *
 * @param string $content A website
 * @return string HTML content
 */

public function get_innerHTML( $content, $id ) {
	// use mb_string if available
	if ( function_exists( 'mb_convert_encoding' )){
		/* Detect character encoding with current detect_order */
		$encoding =  mb_detect_encoding($content);
		if( strtoupper($encoding ) != "UTF-8")
			$content = mb_convert_encoding($content, 'UTF-8');
	}
	$dom= new DOMDocument();
	$dom->loadHTML( $content );
	$dom->preserveWhiteSpace = false;	
	$element = $dom->getElementById( $id );
	$innerHTML = $this->innerHTML( $element );
	return( $innerHTML ); 
}

/**	 
 * Helper, returns the innerHTML of an element
 *
 * @param object DOMElement
 * @return string one element's HTML content
 */
public function innerHTML( $contentdiv ) {
	$r = '';
	$elements = $contentdiv->childNodes;
	foreach( $elements as $element ) { 
		if ( $element->nodeType == XML_TEXT_NODE ) {
			$text = $element->nodeValue;
			// IIRC the next line was for working around a
			// WordPress bug
			//$text = str_replace( '<', '&lt;', $text );
			$r .= $text;
		}	 
		// FIXME we should return comments as well
		elseif ( $element->nodeType == XML_COMMENT_NODE ) {
			$r .= '';
		}	 
		else {
			$r .= '<';
			$r .= $element->nodeName;
			if ( $element->hasAttributes() ) { 
				$attributes = $element->attributes;
				foreach ( $attributes as $attribute )
					$r .= " {$attribute->nodeName}='{$attribute->nodeValue}'" ;
			}	 
			$r .= '>';
			$r .= $this->innerHTML( $element );
			$r .= "</{$element->nodeName}>";
		}	 
	}	 
	return $r;
}
	

	//============================ #2
	function Visit($url){
		   $agent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";
		   $ch=curl_init();
		   curl_setopt ($ch, CURLOPT_URL,$url );
		   curl_setopt($ch, CURLOPT_USERAGENT, $agent);
		   curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		   curl_setopt ($ch,CURLOPT_VERBOSE,false);
		   curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		   curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, FALSE);
		   curl_setopt($ch,CURLOPT_SSLVERSION,3);
		   curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, FALSE);
		   $page=curl_exec($ch);
		   //echo curl_error($ch);
		   $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		   curl_close($ch);
		   /** Http Code Status
			100 Continue
			101 Switching Protocols
			200 OK
			201 Created
			202 Accepted
			203 Non-Authoritative Information
			204 No Content
			205 Reset Content
			206 Partial Content
			300 Multiple Choices
			301 Moved Permanently
			302 Found
			303 See Other
			304 Not Modified
			305 Use Proxy
			306 (Unused)
			307 Temporary Redirect
		   */
		   echo $httpcode;
		   if($httpcode >= 200 && $httpcode < 307) 
		   return true;
		   else return false;
	}   
  
function curl_download($Url){
    // is cURL installed yet?
    if (!function_exists('curl_init')){
        die('Sorry cURL is not installed!');
    }
    // OK cool - then let's create a new cURL resource handle
    $ch = curl_init();
    // Now set some options (most are optional)
    // Set URL to download
    curl_setopt($ch, CURLOPT_URL, $Url);
    // Set a referer
    curl_setopt($ch, CURLOPT_REFERER, "http://www.google.com/");
    // User agent
	curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)"); 
    // Include header in result? (0 = yes, 1 = no)
    curl_setopt($ch, CURLOPT_HEADER, 0);
    // Should cURL return or print out the data? (true = return, false = print)
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Timeout in seconds
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
 
    // Download the given URL, and return output
    $output = curl_exec($ch);
 
    // Close the cURL resource, and free system resources
    curl_close($ch);
 
    return $output;
}
//====================================================	      
}





?> 


