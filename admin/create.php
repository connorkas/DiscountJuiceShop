<?php
session_start();
require_once "force_login.inc";
require_once "db.inc.php";

//Prepared Statement to prevent SQLi
if($_REQUEST['name']) {
	$myname = mysqli_real_escape_string($mysqli, $_REQUEST['name']);
	$myprice = mysqli_real_escape_string($mysqli, $_REQUEST['price']);
	$mycalories = mysqli_real_escape_string($mysqli, $_REQUEST['calories']);
	$mypercent = mysqli_real_escape_string($mysqli, $_REQUEST['percent']);

	$stmt = mysqli_prepare($mysqli, "INSERT INTO products (name, price, calories, juicepercent) VALUES (?, ?, ?, ?)") or die('Error in SQL: ' . mysqli_error($mysqli));
	mysqli_stmt_bind_param($stmt, "siii", $myname, $myprice, $mycalories, $mypercent);
	
	// Check for CSRF vulnerability
	if ($_REQUEST['csrf_token'] === $_SESSION['csrf_token']) {
		if(mysqli_stmt_execute($stmt) === TRUE) {
			unset($_SESSION['csrf_token']);
			//Sanitize displayed output to prevent XSS
			$name = htmlentities($myname);
			echo "New product $name created successfully!";
		} else {
			echo "An unexpected error has occured: " . "<br>";
		}	
	}
}

// Create CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(64));

?>
<html>
<body>

<h1>Creating Data</h1>
<p>We're here creating data</p>

<form method="POST">

<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

<label>Name</label>
<input type="text" name="name" pattern="[a-zA-Z]+[a-zA-Z ]+" required>
<br>

<label>Price</label>
<input type="number" pattern="[0-9.]" name='price' min='0' max="999.99" step="0.50" required>
<br>

<label>Calories</label>
<input type="number" pattern="[0-9]" name='calories' min='0' max="9999.99" step="0.50" required>
<br>

<label>Percentage of Juice Contents</label>
<input type="number" pattern="[0-9]" name='percent' min='0' max="999.99" step="1" required>
<br>

<input type="submit" value="Create" name='Create' class='button'>

</form>
<p>Visit <a href="/admin/read.php">Read Page</a><p>
<br><br><br>
<a href="/admin/logout.php">Logout</a>

</body>

</html>
