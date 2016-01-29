<?php 
	
	error_reporting(E_ALL);
	
	include_once __DIR__.'/simple_html_dom.php';
	require 'vendor/autoload.php';
	
	use GuzzleHttp\Client;

	$client = new GuzzleHttp\Client();
	/*
	$request = new \GuzzleHttp\Psr7\Request('GET', 'http://www.google.ca/search?hl=en&q=sandeep');
		$promise = $client->sendAsync($request)->then(function ($response) {
			$html = $response->getBody();
				echo $html;
		});
		$promise->wait();		
	die('halting..');
	*/

	$keywords = array(
		'apple',
		'ball',
		'cat',
		'dog'	
	);
	
	foreach($keywords as $keyword){
		doGoogleSearch($keyword);	
	}
	
	
	function doGoogleSearch($keyword){
		global $client;
		// Send an asynchronous request.
		$request = new \GuzzleHttp\Psr7\Request('GET', 'http://www.google.ca/search?hl=en&q='.$keyword);
		$promise = $client->sendAsync($request)->then(function ($response) {
				$html = $response->getBody();
				echo $html;
		});
		$promise->wait();		
	}