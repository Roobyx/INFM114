<?php
	// $rootPath = $_SERVER['DOCUMENT_ROOT'];
	define('rootPath', $_SERVER['DOCUMENT_ROOT'] .= "/coursework");
	$rp = rootPath . "/client/pages/credentials/login.php";
	define('loginPath', $rp);

	// echo loginPath;

	define('DB_SERVER', 'localhost');
	define('DB_USERNAME', 'infm');
	define('DB_PASSWORD', '114');
	define('DB_NAME', 'infm114');

	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

	
	/* Attempt to connect to MySQL database */
	global $dbLink;
	
	$dbLink = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

	
	// Check connection
	if($dbLink === false){
		die("ERROR: Could not connect. " . mysqli_connect_error());
	}
?>