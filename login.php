<?php
	// Initialize the session
	session_start();

	// Check if the user is already logged in, if yes then redirect him to welcome page
	if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
		header("location: home");
		exit;
	}

	// Define variables and initialize with empty values
	$fNumber = $password = $fNumber_err = $password_err = $login_err = $virgin = "";

	// Processing form data when form is submitted
	if($_SERVER["REQUEST_METHOD"] == "POST") {

		// Check if fNumber is empty
		if(empty(trim($_POST["fNumber"]))) {
			$fNumber_err = "Моля въведете факултетен номер";
		} else {
			$fNumber = trim($_POST["fNumber"]);
		}

		// Check if password is empty
		if(empty(trim($_POST["password"]))) {
			$password_err = "Моля въведете парола";
		} else {
			$password = trim($_POST["password"]);
		}
		
		// Validate credentials
		if(empty($fNumber_err) && empty($password_err)) {

			// Prepare a select statement
			$sql = "SELECT fNumber, password, virgin, latestSemester FROM users WHERE fNumber = ?";

			if($stmt = mysqli_prepare($dbLink, $sql)) {
				// Bind variables to the prepared statement as parameters
				mysqli_stmt_bind_param($stmt, "s", $param_fNumber);

				// Set parameters
				$param_fNumber = $fNumber;

				// Attempt to execute the prepared statement
				if(mysqli_stmt_execute($stmt)) {

					// Store result
					mysqli_stmt_store_result($stmt);

					// Check if fNumber exists, if yes then verify password
					if(mysqli_stmt_num_rows($stmt) == 1) {

						// Bind result variables
						mysqli_stmt_bind_result($stmt, $fNumber, $hashed_password, $virgin, $latestSemester);

						if(mysqli_stmt_fetch($stmt)) {

							if(password_verify($password, $hashed_password)) {

								// Password is correct, so start a new session
								session_start();

								// Store data in session variables
								$_SESSION["loggedin"] = true;
								$_SESSION["fNumber"] = $fNumber;
								$_SESSION["virgin"] = $virgin;
								$_SESSION["latestSemester"] = $latestSemester;

								// Redirect user to welcome page
								header("location: home");
								// $request = "home";
							} else {
								// Password is not valid, display a generic error message
								$login_err = "Не валиден факултетен номер или парола";
							}
						}
					} else {
						// fNumber doesn't exist, display a generic error message
						$login_err = "Не валиден факултетен номер или парола";
					}
				} else {
					echo "Възникна грешка при обработването. Моля опитайте пак.";
				}
				// Close statement
				mysqli_stmt_close($stmt);
			}
		}

		// Close connection
		mysqli_close($dbLink);
	}
?>

<div class="wrapper">
	<h2>Вход</h2>

	<?php 
	if(!empty($login_err)) {
		echo '<div class="alert alert-danger">' . $login_err . '</div>';
	}
	?>

	<form method="post">
		<div class="form-group">
			<label>Факултетен номер</label>
			<input type="text" name="fNumber" class="form-control <?php echo (!empty($fNumber_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $fNumber; ?>">
			<span class="invalid-feedback"><?php echo $fNumber_err; ?></span>
		</div>
		<div class="form-group">
			<label>Парола</label>
			<input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
			<span class="invalid-feedback"><?php echo $password_err; ?></span>
		</div>
		<div class="form-group">
			<input type="submit" class="btn btn-primary" value="Влез">
		</div>
		<p>Не сте регистриран? <a href="register"> Създай акаунт тук</a>.</p>
	</form>
</div>