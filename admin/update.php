<?php
require_once "force_login.inc";
require_once "db.inc.php";

$myid = $_REQUEST['id'];

if($_REQUEST['name']) {
	// Only name is sanitized since all other options only accept integers [0-9]
	// (Prevents XSS)
	$myname = htmlentities(mysqli_real_escape_string($mysqli, $_REQUEST['name']));
	$myprice = mysqli_real_escape_string($mysqli, $_REQUEST['price']);
	$mycalories = mysqli_real_escape_string($mysqli, $_REQUEST['calories']);
	$mypercent = mysqli_real_escape_string($mysqli, $_REQUEST['percent']);

	$stmt = mysqli_prepare($mysqli, "UPDATE products SET name=?, price=?, calories=?, juicepercent=? WHERE id=?") or die('Error in SQL: ' . mysqli_error($mysqli));
	mysqli_stmt_bind_param($stmt, "siiii", $myname, $myprice, $mycalories, $mypercent, $myid);

	// Check for CSRF validation
	if($_REQUEST['csrf_token'] === $_SESSION['csrf_token']) {
		if(mysqli_stmt_execute($stmt) === TRUE) {
			unset($_SESSION['csrf_token']);
			echo "New product $myname updated successfully!";
		} else {
			echo "An unexpected error has occured." . "<br>";
		}
	}
}

// Load placeholders into update page
$stmt = mysqli_prepare($mysqli, "SELECT * FROM products WHERE id=?");
mysqli_stmt_bind_param($stmt, "i", $myid);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_array($result);

// Create CSRF Token upon POST submission
if(array_key_exists('Update', $_POST)) {
	create_csrf();
}
function create_csrf() {
	$_SESSION['csrf_token'] = bin2hex(random_bytes(64));
}

//Sanitize placeholder data to prevent XSS
$name = htmlentities($row['name']);
$price = htmlentities($row['price']);
$calories = htmlentities($row['calories']);
$juicepercent = htmlentities($row['juicepercent']);
$id = htmlentities($row['id']);
?>
<html>
<body>

<h1>Update Product</h1>
<p>This is an update product page, make your changes and submit</p>

<form method="POST">

<input type='hidden' name='id' value='<?= $id ?>'>
<input type='hidden' name='csrf_token' value='<?= $_SESSION['csrf_token'] ?>'>
<br>

<label>Name</label>
<input type='text' pattern="[a-zA-Z]+[a-zA-Z ]+"  name='name' value='<?= $name ?>' required>
<p style="font-size:10px;">Name must be at least 2 characters and not contain numbers</p>

<label>Price</label>
<input type='number' pattern="[0-9.]" name='price' value="<?= $price ?>" min='0' max="999.99" step="0.50" required >
<p style="font-size:10px;">Price must only contain numbers</p>

<label>Calories</label>
<input type='number' pattern="[0-9]" name='calories' value="<?= $calories ?>" min='0' max="9999.99" step="1" required >
<p style="font-size:10px;">Calories must only contain numbers</p>

<label>Juice Percentage</label>
<input type='number' pattern="[0-9]" name='percent' value="<?= $juicepercent ?>" min='0' max="99.99" step="0.01" required >
<p style="font-size:10px;">Juice Percentage must only contain numbers</p>

<input type='submit' value='Update' name='Update' class='button'>

</form>

<p>
Visit <a href="/admin/create.php">Create Page</a><br>
Visit <a href="/admin/read.php">Read Page</a>
<p>
<br><br><br>
<a href="/admin/logout.php">Logout</a>

</body>
</html>
