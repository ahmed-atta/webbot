<?php
$patterns = array ('/(05|\+9665|009665)(\d{8})/','/(968|971)|(\+968|\+971|00968|00971)(\d{9})/');
$replaces = array ('9665\2','\1\3');

$pattern = '/^(05|\+9665|009665|9665)(\d{8})/';
$replace = '9665\2';
//================================
$patt1 = '/^(971|\+971|00971)(\d{9})/';
$rep1 = '971\2';
//=================================
$patt2 = '/^(968|\+968|00968)(\d{9})/';
$rep2 = '968\2';


if(preg_match($pattern, $number, $match1)){
	echo preg_replace($pattern, $replace, $match1[0],1);
} else if(preg_match($patt1,$number, $match2)){
	echo preg_replace($patt1, $rep1, $match2[0],1);
} else if(preg_match($patt2, $number, $match3)){
	echo preg_replace($patt2, $rep2, $match3[0],1);
}

//

?>