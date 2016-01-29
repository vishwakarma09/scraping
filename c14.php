<?php
	
error_reporting(0);

include_once __DIR__.'/simple_html_dom.php';
include_once __DIR__.'/ASPBrowser.php';
include_once __DIR__.'/connection.php';
//include_once __DIR__.'/DBFunctions.php';

function getExtractedDataC14States(simple_html_dom $dom, $levels = null) {
		$insertArray = array();
	
		$tbody = $dom->find('#tableReportTable tbody tr');
    foreach($tbody as $tr) {
			$insertRow = array();
			for($i=0;$i<=$levels;$i++){
				if($i==1){
					$td = $tr->find('td a[id]',0);
					$id = $td->id;
					$state = $td->innertext;
					$target = trim($id);
					$insertRow[] = trim($state);
					continue;
				}
				$td = $tr->find('td', $i)->innertext;
				$insertRow[] = trim($td);
				
				if($levels == $i) $insertRow[] = $target;
			}
			$insertArray[] = $insertRow;
			unset($insertRow);
    }
		
		//remove first and last array elements
		array_pop($insertArray); // remove from end
		array_shift($insertArray); //remove from beginning
		array_shift($insertArray); //remove from beginning
		array_shift($insertArray); //remove from beginning
		array_shift($insertArray); //remove from beginning
		array_shift($insertArray); //remove from beginning
		
		return $insertArray;
}

function doDataInsertScrapec14States($data){
	global $conn;
	foreach($data as $row){
		$sql = "insert into scrape_c14_states values('" . implode("','",$row) . "');";
		echo $sql;
		mysqli_query($conn, $sql) or trigger_error(mysqli_error($conn)." ".$sql);
	}
	return true;
}

function getExtractedDataB1DistrictsAll(simple_html_dom $dom, $levels = null) {
		$insertArray = array();
	
		$tbody = $dom->find('#tableReportTable tbody tr');
		$needle = 1;
    foreach($tbody as $tr){
			$insertRow = array();
			for($i=0;$i<=$levels;$i++){
				if($i==0){
					$td = $tr->find('td',0)->innertext;
					$insertRow[] = trim($td);
					continue;
				}else	if($i==1){	//for target id and district name
					$td = $tr->find('td a[id]',0);
					$id = $td->id;
					$district = $td->innertext;
					$insertRow[] = trim($district);
					$insertRow[] = trim($id);
					continue;
				}
				$td = $tr->find('td a[id]', $i-1)->innertext;
				$insertRow[] = trim($td);
			}
			
			if($needle == 2){
		//		print_r($insertRow); die();	
			}
			
			$needle++;
			$insertArray[] = $insertRow;
			unset($insertRow);
    }
		
		//remove first and last array elements
		array_pop($insertArray);
		array_shift($insertArray);
		return $insertArray;
}

function Start(){
	global $conn;
	
	$url = 'http://indiawater.gov.in/IMISReports/Reports/EntryStatus/Rep_FinancialPhysicalProgressReport_S.aspx?Rep=0&Rtype=PH';
	$browser = new ASPBrowser();
	$html = $browser->doGetRequest($url); // get form
	
	$data = getExtractedDataC14States($html, 25);
	echo json_encode($data);
	//doDataInsertScrapec14States($data);
	
	// stored state list. now processing state BIHAR
	
	//$sql = "SELECT * FROM `scrape_c14_states` WHERE `c14_2`='BIHAR'";
	//$state_result = mysqli_query($conn, $sql) or trigger_error(mysqli_error($conn)." ".$sql);
	//$state_row = mysqli_fetch_assoc($state_result);
	
	//	$seed = str_replace('_','$',$state_row['target']);
		$seed = 'ctl00$upPnl|ctl00$ContentPlaceHolder$rpt$ctl05$lkbstate';
	
		$postArray = array(
			'__EVENTTARGET'		=>	$seed,
			'__EVENTARGUMENT'	=>	'',
			'__LASTFOCUS'			=>	'',
			'__ASYNCPOST'			=>	'true',
			'ctl00$ScriptManager1'	=>	'ctl00$upPnl|'.$seed,
			'ctl00$ContentPlaceHolder$ddfinyear'	=>	'2015-2016',
			'ctl00$ContentPlaceHolder$ddState'		=>	'005',
			'ctl00$ContentPlaceHolder$RadioButtonListType'	=>	'All',
			'ctl00$ddLanguage'	=>	''
		);
		
		//echo json_encode($postArray); die();
		$browser->exclude = array('ctl00$ContentPlaceHolder$btnGO');	//setup session on server
		$browser->exclude = array('Map');	
		$browser->exclude = array('aspnetForm');
		$browser->exclude = array('ctl00$ImgCon');
		$browser->exclude = array('cctl00$ImgBri');
		$browser->exclude = array('ctl00$convertWord');
		$browser->exclude = array('ctl00$convertEXCEL');
		$browser->exclude = array('ctl00$ImgCon');
		
	  $browser->doPostRequest($url, $postArray);
		
		$url = "http://indiawater.gov.in/IMISReports/Reports/EntryStatus/Rep_FinancialPhysicalProgressReport_D.aspx?Rep=0&Rtype=PH";
		$html = $browser->doGetRequest($url); // get form
		$data = getExtractedDataC14DistrictsAll($html, 5);
		echo json_encode($data);
		doDataInsertScrapeB1DistrictsAll($data, $state_row['id']);
		

		
}

//StateLink();
Start();




?>