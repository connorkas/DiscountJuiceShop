<?php

require_once "force_login.inc";
require_once "db.inc.php";

// Set CSRF token make strictly to pass to delete.php
$_SESSION['csrf_token'] = bin2hex(random_bytes(64));

?>
<html>
<style>
/* Cleans up how the buttons displayed next to products */
form{ display: inline-block; }
</style>
<body>

<h1>Read Page</h1>
<p>Here is the data in my database</p>

<?php
// Prepared Statement to prevent SQLi
$stmt = mysqli_prepare($mysqli, "SELECT * FROM products") or die('Error in SQL: ' . mysqli_error($mysqli));
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

while($row = mysqli_fetch_array($result)) {
	//Sanitize displayed output to prevent XSS
	$name = htmlentities($row['name']);
	$price = htmlentities($row['price']);
	$calories = htmlentities($row['calories']);
	$juicepercent = htmlentities($row['juicepercent']);
	$id = htmlentities($row['id']);
	echo "{$name} \${$price} {$calories}cal {$juicepercent}% ";

?>
<!-- Update Button -->
<form method="POST" action="/admin/update.php">
<input type='hidden' name='id' value="<?= $id ?>">
<input type='submit' value='Update' name='Update' class='button'>
</form>

<!-- Delete Button -->
<form method="POST" action="/admin/delete.php">
<input type='hidden' name='csrf_token' value='<?= $_SESSION['csrf_token'] ?>'>
<input type='hidden' name='id' value="<?= $id ?>">
<input type='submit' value='Delete' name='Delete' class='button'>
</form>
<br>
<?php
} // Ends the while() loop 
?>
</body>

<p>Visit <a href="/admin/create.php">Create Page</a><p>
<br><br><br>
<a href="/admin/logout.php">Logout</a>


</html>
