<?php
session_start();
//Create CSRF Token
$_SESSION['csrf_token'] = bin2hex(random_bytes(64));
require_once "db.inc.php";

//Whitelisted redirects that HTTP GET & POST requests
//are allowed to make. Prevents malicious redirect(s) & Header Injection
$redirects = ['/admin/create.php', '/admin/read.php', '/admin/update.php', '/admin/delete.php', '/admin/login.php?redirect=/admin/create.php', '/admin/login.php?redirect=/admin/read.php', '/admin/login.php?redirect=/admin/update.php', '/admin/login.php?redirect=/admin/delete.php'];

$myusername = mysqli_real_escape_string($mysqli, $_REQUEST['username']);
$mypassword = mysqli_real_escape_string($mysqli, $_REQUEST['password']);

// Prepared SQL statement to prevent SQLi
$stmt = mysqli_prepare($mysqli, "SELECT * FROM users WHERE username=? AND password = SHA2(?, 256)") or die('Error in SQL: ' . mysqli_error($mysqli));
mysqli_stmt_bind_param($stmt, "ss", $myusername, $mypassword);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_array($result);

?>
<html>
<head>
<title>Login Page</title>
</head>
<body>
<h1>Admin Login</h1>

<?php
// uname&pword check, along with CSRF check
if(!empty($row) && $_SESSION['csrf_token'] === $_REQUEST['csrf_token']) {
	// Unset CSRF token to prevent reuse
	unset($_SESSION['csrf_token']);
	
	// Successful Login
	session_destroy();
	session_start();
	session_regenerate_id();
	
	$_SESSION['username'] = $row['username'];

} elseif($mypassword || $myusername) {
	// Failed Login
	echo "<p>Wrong username OR password</p>";
}

if($_SESSION['username'] && $_REQUEST['redirect']!="") {
	// Protect against Header Injection
	if(in_array($_REQUEST['redirect'], $redirects)) {
		// Redirect back to original page instead of saying welcome
		// (If originally came to login.php from redirect)
		header("Location: {$_REQUEST['redirect']}");		
		exit();
	}
} elseif($_SESSION['username']) {
	// Welcome user if they directly accessed the login.php page
	echo "Welcome " . htmlentities($myusername) . "!";
	echo "
	<p>Visit <a href=\"/admin/create.php\">Create Page</a><br>

	Visit <a href=\"/admin/read.php\">Read Page</a><br><br>

	<a href=\"/admin/logout.php\">Logout</a>
	</p>
	";
	exit();

}  else {
	//Sanitize redirect
	$redirect = htmlentities($_REQUEST['redirect']);
	// Preforms all HTML code below if not logged in
?>

<form method="POST">

<input name="redirect" type='hidden' value="<?= $redirect ?>">
<input name='csrf_token' type='hidden' value="<?= $_SESSION['csrf_token'] ?>">

<label>Username</label>
<input type='text' name='username' pattern="[a-zA-Z0-9]+[0-9a-zA-Z ]+" title="Alphanumeric characters only" required>
<br>
<label>Password</label>
<input type='password' name='password' pattern="[a-zA-Z0-9]+[0-9a-zA-Z ]+" required>
<br>

<input type='submit' value='Login'>

</form>
<?php
} // End PHP conditional of authentication
?>
</body>
</html>
