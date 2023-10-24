<?php
require_once('../Bookorama-App/lib/db_login.php');
if (isset($_POST["submit"])) {
	$valid = TRUE;
	$username = $_POST['username'];
	if ($username == '') {
		$error_username = "Username is required";
		$valid = FALSE;
	}

	$password = $_POST['password'];
	if ($password == '') {
		$error_password = "Password is required";
		$valid = FALSE;
	}

	//insert data into database
	if ($valid) {
		//Begin Transaction
		$db->autocommit(FALSE);
		$db->begin_transaction();
		$query_ok = TRUE;

		//Asign a query
		$query1 = " SELECT * FROM users WHERE username = ? AND password = ? " ;
		$stmt = $db->prepare($query1);

		//Check for error
		if (!$stmt) {
			$query_ok = FALSE;
			die("Could not query the database: <br />" . $db->error . "<br>Query: " . $stmt);
		}

		$stmt->bind_param("ss", $username, $password);
		$stmt->execute();
		$result = $stmt->get_result();

		if (!$result) {
			die("Could not query the database: <br />" . $db->error . 'query = ' . $query);
		} else {
			if ($result->num_rows < 1) {
				$error_login = "Username atau password salah";
			} else {
				echo "<br>Login Berhasil";
			}
		}

		//commit the query
		if ($query_ok) {
			$db->commit();
			echo "Eksekusi berhasil!!!";
		} else {
			$db->rollback();
			echo "Eksekusi Gagal!!!";
		}

		//close db connection
		$stmt->close();
		$db->close();
	}
}
?>
<?php include('./header.php') ?>
<br>
<div class="card">
	<div class="card-header">Login</div>
	<div class="card-body">
		<div class="error">
			<?php if (isset($error_login))
				echo $error_login; ?>
		</div>
		<form method="POST" autocomplete="on" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
			<div class="form-group">
				<label for="name">Username:</label>
				<input type="text" class="form-control" id="username" name="username" maxlength="50" value="<?php if (isset($username))
					echo $username; ?>">
				<div class="error">
					<?php if (isset($error_username))
						echo $error_username; ?>
				</div>
			</div>
			<div class="form-group">
				<label for="name">Password:</label>
				<input type="password" class="form-control" id="password" name="password" maxlength="50">
				<div class="error">
					<?php if (isset($error_password))
						echo $error_password; ?>
				</div>
			</div>
			<button type="submit" class="btn btn-primary" name="submit" value="submit">Submit</button>&nbsp;
			<a href="view_customer.php" class="btn btn-secondary">Cancel</a>
		</form>
	</div>
</div>
<?php include('./footer.php') ?>