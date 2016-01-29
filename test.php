lll<?php 
	error_reporting(E_ALL);
	echo "hooo";
	$filename = "000000-999999.23-01-2016050500.00-00-0000000000.999999";
	echo $filename;
	$date_filename = substr($filename,14, 10);
	$db_filename = str_replace('-','_',$date_filename);
	echo $db_filename;