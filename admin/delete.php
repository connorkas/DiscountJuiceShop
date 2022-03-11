<?php

require_once "force_login.inc";
require_once "db.inc.php";

$myid =$_REQUEST['id'];

// Prepared Statement to prevent SQLi
$stmt = mysqli_prepare($mysqli, "DELETE FROM products WHERE id=?") or die('Error in SQL: ' . mysqli_error($mysqli));
mysqli_stmt_bind_param($stmt, "i", $myid);

if($_REQUEST['csrf_token'] === $_SESSION['csrf_token']) {
	if(mysqli_stmt_execute($stmt) === TRUE) {
		unset($_SESSION['csrf_token']);
		echo "Deleted successfully";
	} else {
		echo "An unexpected error has occured." . "<br>";
	}
}

?>
<html>
<body>

<p>
Return to <a href="/admin/create.php">Create Page</a><br>
Return to <a href="/admin/read.php">Read Page</a>
<p>


</body>
</html>
