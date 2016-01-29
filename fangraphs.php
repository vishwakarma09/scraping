<?php 
	error_reporting(E_ALL);

	include_once __DIR__.'/simple_html_dom.php';
	include_once __DIR__.'/ASPBrowser.php';
//	include_once __DIR__.'/connection.php';

	
	Start();
	
	
	
function Start(){
	
	global $conn;
	
	//get base URL screen by GET
	$url = 'http://www.fangraphs.com/projections.aspx?pos=all&stats=bat&type=steamer';
	$browser = new ASPBrowser();
	$html = $browser->doGetRequest($url); // get form
	
	echo $html;
	
	$postArray = array(
		'__EVENTTARGET'		=>	'ProjectionBoard1%24dg1%24ctl00%24ctl02%24ctl00%24ctl07',	//it is incremented by 2 so 09, 11, ...
		'__SCROLLPOSITIONY'	=>	'520'
	);

//	$browser->exclude = array('ctl00$ImgCon');	//setup session on server

	$html = $browser->doPostRequest($url, $postArray);
	
	echo $html;
	//setting state finish
}