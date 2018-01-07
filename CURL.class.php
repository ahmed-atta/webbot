<?php
class c_CURL 
{ 
     protected $_useragent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1'; 
     protected $_url; 
     protected $_followlocation; 
     protected $_timeout; 
     protected $_maxRedirects; 
     protected $_cookieFileLocation = './cookie.txt'; 
     protected $_post; 
     protected $_postFields; 
     protected $_referer ="http://www.google.com"; 

     protected $_session; 
     protected $_webpage; 
     protected $_includeHeader; 
     protected $_noBody; 
     protected $_status; 
     protected $_binaryTransfer; 
     public    $authentication = 0; 
     public    $auth_name      = ''; 
     public    $auth_pass      = ''; 

     public function useAuth($use){ 
       $this->authentication = 0; 
       if($use == true) $this->authentication = 1; 
     } 

     public function setName($name){ 
       $this->auth_name = $name; 
     } 
     public function setPass($pass){ 
       $this->auth_pass = $pass; 
     } 

     public function __construct($url,$followlocation = true,$timeOut = 30,$maxRedirecs = 4,$binaryTransfer = false,$includeHeader = false,$noBody = false) 
     { 
         $this->_url = $url; 
         $this->_followlocation = $followlocation; 
         $this->_timeout = $timeOut; 
         $this->_maxRedirects = $maxRedirecs; 
         $this->_noBody = $noBody; 
         $this->_includeHeader = $includeHeader; 
         $this->_binaryTransfer = $binaryTransfer; 

         $this->_cookieFileLocation = dirname(__FILE__).'/cookie.txt'; 

     } 

     public function setReferer($referer){ 
       $this->_referer = $referer; 
     } 

     public function setCookiFileLocation($path) 
     { 
         $this->_cookieFileLocation = $path; 
     } 

     public function setPost ($postFields) 
     { 
        $this->_post = true; 
        $this->_postFields = $postFields; 
     } 

     public function setUserAgent($userAgent) 
     { 
         $this->_useragent = $userAgent; 
     } 

     public function createCurl($url = 'nul') 
     { 
        if($url != 'nul'){ 
          $this->_url = $url; 
        } 
         $s = curl_init(); 
         curl_setopt($s,CURLOPT_URL,$this->_url); 
         curl_setopt($s,CURLOPT_HTTPHEADER,array('Expect:')); 
         curl_setopt($s,CURLOPT_TIMEOUT,$this->_timeout); 
         curl_setopt($s,CURLOPT_MAXREDIRS,$this->_maxRedirects); 
         curl_setopt($s,CURLOPT_RETURNTRANSFER,true); 
         curl_setopt($s,CURLOPT_FOLLOWLOCATION,$this->_followlocation); 
         curl_setopt($s,CURLOPT_COOKIEJAR,$this->_cookieFileLocation); 
         curl_setopt($s,CURLOPT_COOKIEFILE,$this->_cookieFileLocation); 

         if($this->authentication == 1){ 
           curl_setopt($s, CURLOPT_USERPWD, $this->auth_name.':'.$this->auth_pass); 
         } 
         if($this->_post){ 
             curl_setopt($s,CURLOPT_POST,true); 
             curl_setopt($s,CURLOPT_POSTFIELDS,$this->_postFields); 

         } 
         if($this->_includeHeader) { 
               curl_setopt($s,CURLOPT_HEADER,true); 
         } 
         if($this->_noBody) { 
             curl_setopt($s,CURLOPT_NOBODY,true); 
         } 
         /* 
         if($this->_binary) 
         { 
             curl_setopt($s,CURLOPT_BINARYTRANSFER,true); 
         } 
         */ 
         curl_setopt($s,CURLOPT_USERAGENT,$this->_useragent); 
         curl_setopt($s,CURLOPT_REFERER,$this->_referer); 

         $this->_webpage = curl_exec($s); 
         $this->_status = curl_getinfo($s,CURLINFO_HTTP_CODE); 
         curl_close($s); 
     } // End createCurl function

   public function getHttpStatus() 
   { 
       return $this->_status; 
   } 

   public function __tostring(){ 
      return $this->_webpage; 
   } 
} 
//========================================================
//======================================== You can use this class for fast entry 
// http://php.net/manual/en/book.curl.php
class cURL 
{ 
	var $headers; 
	var $user_agent; 
	var $compression; 
	var $cookie_file; 
	var $proxy; 
	function cURL($cookies=TRUE,$cookie='cookies.txt',$compression='gzip',$proxy='') { 
		$this->headers[] = 'Accept: image/gif, image/x-bitmap, image/jpeg, image/pjpeg'; 
		$this->headers[] = 'Connection: Keep-Alive'; 
		$this->headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8'; 
		$this->user_agent = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.0.3705; .NET CLR 1.1.4322; Media Center PC 4.0)'; 
		$this->compression=$compression; 
		$this->proxy=$proxy; 
		$this->cookies=$cookies; 
		if ($this->cookies == TRUE) $this->cookie($cookie); 
	} 
	function cookie($cookie_file) { 
		if (file_exists($cookie_file)) { 
		$this->cookie_file=$cookie_file; 
		} else { 
		fopen($cookie_file,'w') or $this->error('The cookie file could not be opened. Make sure this directory has the correct permissions'); 
		$this->cookie_file=$cookie_file; 
		fclose($this->cookie_file); 
		} 
	} 
function url_exist($url){//se passar a URL existe
	    $c = curl_init();
	    curl_setopt($c,CURLOPT_URL,$url);
	    curl_setopt($c,CURLOPT_HEADER,1);//get the header
	    curl_setopt($c,CURLOPT_NOBODY,1);//and *only* get the header
	    curl_setopt($c,CURLOPT_RETURNTRANSFER,1);//get the response as a string from curl_exec(), rather than echoing it
	    curl_setopt($c,CURLOPT_FRESH_CONNECT,1);//don't use a cached version of the url
	    if(!curl_exec($c)){
	        //echo $url.' inexists';
	        return false;
	    }else{
	        //echo $url.' exists';
	        return true;
	    }
	    //$httpcode=curl_getinfo($c,CURLINFO_HTTP_CODE);
	    //return ($httpcode<400);
	}
	function get($url) { 
		$process = curl_init($url); 
		curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers); 
		curl_setopt($process, CURLOPT_HEADER, 0); 
		curl_setopt($process, CURLOPT_USERAGENT, $this->user_agent); 
		if ($this->cookies == TRUE) {
			curl_setopt($process, CURLOPT_COOKIEFILE, $this->cookie_file); 
			curl_setopt($process, CURLOPT_COOKIEJAR, $this->cookie_file); 
		}
		curl_setopt($process,CURLOPT_ENCODING , $this->compression); 
		curl_setopt($process, CURLOPT_TIMEOUT, 30); 
		if ($this->proxy) curl_setopt($process, CURLOPT_PROXY, $this->proxy); 
		curl_setopt($process, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1); 
		$return = curl_exec($process); 
		curl_close($process); 
		return $return; 
	} 
	function post($url,$data) { 
		$process = curl_init($url); 
		curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers); 
		curl_setopt($process, CURLOPT_HEADER, 1); 
		curl_setopt($process, CURLOPT_USERAGENT, $this->user_agent); 
		if ($this->cookies == TRUE) curl_setopt($process, CURLOPT_COOKIEFILE, $this->cookie_file); 
		if ($this->cookies == TRUE) curl_setopt($process, CURLOPT_COOKIEJAR, $this->cookie_file); 
		curl_setopt($process, CURLOPT_ENCODING , $this->compression); 
		curl_setopt($process, CURLOPT_TIMEOUT, 30); 
		if ($this->proxy) curl_setopt($process, CURLOPT_PROXY, $this->proxy); 
		curl_setopt($process, CURLOPT_POSTFIELDS, $data); 
		curl_setopt($process, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1); 
		curl_setopt($process, CURLOPT_POST, 1); 
		$return = curl_exec($process); 
		curl_close($process); 
		return $return; 
	} 
	function error($error) { 
		echo "<center><div style='width:500px;border: 3px solid #FFEEFF; padding: 3px; background-color: #FFDDFF;font-family: verdana; font-size: 10px'><b>cURL Error</b><br>$error</div></center>"; 
		die; 
	} 
} 



?>