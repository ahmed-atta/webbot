<?php
/***********************************************************************
Webbot defaults (scope = global)                                       
----------------------------------------------------------------------*/
# Define how your webbot will appear in server logs
define("WEBBOT_NAME", "Googlebot");  //USER AGENT 

# Length of time cURL will wait for a response (seconds)
define("CURL_TIMEOUT", 25);

define('DS', DIRECTORY_SEPARATOR);
# Location of your cookie file. (Must be fully resolved local address)
define("COOKIE_FILE", dirname(__FILE__).DS."cookie.txt");

# DEFINE METHOD CONSTANTS
define("HEAD", "HEAD");
define("GET",  "GET");
define("POST", "POST");

# DEFINE HEADER INCLUSION
define("EXCL_HEAD", FALSE);
define("INCL_HEAD", TRUE);
?>