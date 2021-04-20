<?php
	// Initialize the session
	session_start();
	require __DIR__ . '/vendor/autoload.php';

	// Include config file
	require_once "./config/config.php";
	
	if($_ENV === 'local') {
		$d = '\\';
		// Omit the coursework folder name
		$request = str_replace("/coursework/", "", $_SERVER['REQUEST_URI']);
	} else {
		$d = '/';
		$request = str_replace("/", "", $_SERVER['REQUEST_URI']);
	}

	$pages = $d . "client" . $d . "pages" . $d;

	if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true ) {

		switch ($request) {
			case 'login':
			case '/login':
			case 'login.php':
				include("./login.php");
				break;

			case 'register':
				include("./register.php");
				break;
			default:
				include("./login.php");
				break;
		}
		include "./client/layout/top_layout_non_logged.php";
		
		echo "</div>";

		exit;
	} elseif(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true ) {
		// Get the requrest and insert the expected partial
		switch ($request) {

			// Take into account multiple options for root
			case '/' :
			case '' :
			case 'home' :

				include "./client/layout/top_layout.php";
				require __DIR__ . $pages . 'home' . $d . 'home.php';
				echo "</div>";
				break;

			case 'logout' :
				require __DIR__ . $pages . 'credentials' . $d . 'logout.php';
				break;

			case 'change-password':
				include "./client/layout/top_layout.php";

				require __DIR__ . $pages . 'credentials' . $d . 'change-password.php';
				break;

			case 'settings':
				include "./client/layout/top_layout.php";

				require __DIR__ . $pages . 'settings' . $d . 'settings.php';
				break;

			case 'setup':
				include "./client/layout/top_layout.php";

				require __DIR__ . $pages . 'setup' . $d . 'setup.php';
				break;

			default:
				include "./client/layout/top_layout.php";

				http_response_code(404);
				require __DIR__ . $pages . 'service' . $d . '404.php';
				break;
		}
	}

	// Include Footer
	include "./client/layout/footer.php";
?>