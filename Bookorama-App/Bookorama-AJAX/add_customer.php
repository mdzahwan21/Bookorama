<!--
File : user_form.html
Deskripsi : menampilkan isi input form dengan ajax
-->
<?php include('../header.php');
require_once('../lib/db_login.php');

?>

<script src="ajax.js"></script>



<br>
<div class="card">
	<div class="card-header">Add Customers Data</div>
	<div class="card-body">

		<form method="GET" autocomplete="on">
			<div class="form-group">
				<label for="name">Name:</label>
				<input type="text" class="form-control" id="name" name="name">
			</div>
			<div class="form-group">
				<label for="address">Address:</label>
				<textarea class="form-control" id="address" name="address"></textarea>
			</div>
			<div class="form-group">
				<label for="city">City:</label>
				<select name="city" id="city" class="form-control" required>
					<option value="Airport West">Airport West</option>
					<option value="Box Hill">Box Hill</option>
					<option value="Yarraville">Yarraville</option>
				</select>
			</div><br>
			<button type="button" class="btn btn-primary" onclick="add_customer_post()">Add</button>
		</form>
		<br>
		<div id="add_response"></div>
	</div>
</div>
<?php include('../footer.php') ?>