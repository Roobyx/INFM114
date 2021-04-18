<?php
	// Variables to use
	$fNumber = "";
	$password = "";
	$confirm_password = "";
	$fNumber_err = "";
	$password_err = "";
	$confirm_password_err = "";

	// TODO: Create a "Login successfull message on the login page, or a new page + email confirmation
	// POST method
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		// Validations

		// Validation - fNumber
		// Checking no fNumber passed
		if (empty(trim($_POST["fNumber"]))) {
			$fNumber_err = "Факултетният номер е задължителен";
		} else {
			// Check if fNumber is numeric only
			if(!is_numeric(trim($_POST["fNumber"]))) {
				$fNumber_err = "Факултетният номер трябва да се състои само от цифри";
			} else {
				// Check if fNumber is longer than 4 symbols
				if(strlen(trim($_POST["fNumber"])) < 4) {
					$fNumber_err = "Факултетният номер не може да бъде по-малък от 4 цифри";
				} else {
					// SQL SELECT assembly
					$sql = "SELECT fNumber FROM users WHERE fNumber = ?";
					
					if ($stmt = mysqli_prepare($dbLink, $sql)) {
						// Bind variables to the prepared statement as parameters
						mysqli_stmt_bind_param($stmt, "s", $param_fNumber);
						
						// Set parameters
						$param_fNumber = trim($_POST["fNumber"]);
						
						if (mysqli_stmt_execute($stmt)) {
							/* store result */
							mysqli_stmt_store_result($stmt);
							
							if (mysqli_stmt_num_rows($stmt) == 1) {
								$fNumber_err = "Този факултетен номер е вече регистриран";
							} else {
								$fNumber = trim($_POST["fNumber"]);
							}
						} else {
							echo "Възникна грешка при обработването. Моля опитайте пак.";
						}

						// Close statement
						mysqli_stmt_close($stmt);
					}
				}
			}
		}
		
		// Validation - Password
		if (empty(trim($_POST["password"]))) {
			// Empty passsord
			$password_err = "Паролата е задължителна";
		} elseif (strlen(trim($_POST["password"])) < 6) {
			// Password size
			$password_err = "Паролата трябва да е поне 6 символа";
		} else {
			// Password POST assembly and whitespace removal
			$password = trim($_POST["password"]);
		}
		
		// Validation - Second password
		if (empty(trim($_POST["confirm_password"]))) {
			$confirm_password_err = "Повторението на паролата е задължително";
		} else {
			$confirm_password = trim($_POST["confirm_password"]);
			if (empty($password_err) && ($password != $confirm_password)) {
				$confirm_password_err = "Паролите не са еднакви";
			}
		}
		
		// Check input errors before inserting in database
		if (empty($fNumber_err) && empty($password_err) && empty($confirm_password_err)) {
			
			// INSERT assembly
			$sql = "INSERT INTO users (fNumber, password) VALUES (?, ?)";

			if ($stmt = mysqli_prepare($dbLink, $sql)) {
				// Bind variables to the prepared statement as parameters
				mysqli_stmt_bind_param($stmt, "ss", $param_fNumber, $param_password);
				
				// Set parameters
				$param_fNumber = $fNumber;
				// Create the password hash
				$param_password = password_hash($password, PASSWORD_DEFAULT);
				
				// Attempt to execute the prepared statement
				if (mysqli_stmt_execute($stmt)) {
					// Redirect to login page
					header("location: login");

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
	<h2>Регистрация</h2>
	<p>Попълнете всички полета.</p>
	<form method="post">
		<div class="form-group">
			<label>Факултетен Номер</label>
			<input type="text" name="fNumber" class="form-control <?php echo (!empty($fNumber_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $fNumber; ?>">
			<span class="invalid-feedback"><?php echo $fNumber_err; ?></span>
		</div>    
		<div class="form-group">
			<label>Парола</label>
			<input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
			<span class="invalid-feedback"><?php echo $password_err; ?></span>
		</div>
		<div class="form-group">
			<label>Повторение на паролата</label>
			<input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
			<span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
		</div>
		<div class="form-group">
			<input type="submit" class="btn btn-primary" value="Submit">
			<input type="reset" class="btn btn-secondary ml-2" value="Reset">
		</div>
		<p><a href="login">Влезте от тук</a> ако вече имате акаунт</p>
	</form>
</div>