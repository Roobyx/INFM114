<?php
	// Initialize the session
	session_start();
	require __DIR__ . '/vendor/autoload.php';

// Omit the coursework folder name
	// TODO!: Remove or change when uploaded to a non localhost server
	$request = str_replace("/coursework/", "", $_SERVER['REQUEST_URI']);

	// Include config file
	require_once "./config/config.php";

	$pages = "\\client\\pages\\";


	if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true ) {
		// Debug
		// echo "logged OUT?";

		switch ($request) {
			case 'login' :
			case 'login.php' :
				include("./login.php");
				break;

			case 'register' :
				include("./register.php");
				break;
			
			default:
				include("./login.php");
				break;
		}

		exit;
	} elseif(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true ) {

		// Get the requrest and insert the expected partial
		switch ($request) {

			// Take into account multiple options for root
			case '/' :
			case '' :
			case 'home' :

				include "./client/layout/top_layout.php";
				require __DIR__ . $pages . 'home\\home.php';
				echo "</div>";
				break;

			case 'logout' :
				require __DIR__ . $pages . 'credentials\\logout.php';
				break;

			case 'change-password':
				include "./client/layout/top_layout.php";

				require __DIR__ . $pages . 'credentials\\change-password.php';
				break;

			case 'settings':
				include "./client/layout/top_layout.php";

				require __DIR__ . $pages . 'settings\\settings.php';
				break;

			case 'setup':
				include "./client/layout/top_layout.php";

				require __DIR__ . $pages . 'setup\\setup.php';
				break;

			default:
				include "./client/layout/top_layout.php";

				http_response_code(404);
				require __DIR__ . $pages . 'service\\404.php';
				break;
		}
	}

	// Include Footer
	include "./client/layout/footer.php";
?>