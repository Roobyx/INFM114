<?php
	// Initialize the session
	// session_start();

	// Unset all of the session variables
	$_SESSION = array();

	// Destroy the session.
	session_destroy();

	// Redirect to login page
	if($_ENV === 'local') {
		header("location: /coursework/login");
	} else {
		header("location: /login");
	}
	exit;
?>