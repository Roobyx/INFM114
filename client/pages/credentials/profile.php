<section>

	<h1 class="my-5">Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to our site.</h1>
	
	<p>
		<a href="change-password.php" class="btn btn-warning">Change Your Password</a>
		<a href="logout.php" class="btn btn-danger ml-3">Sign Out of Your Account</a>
	</p>

</section>