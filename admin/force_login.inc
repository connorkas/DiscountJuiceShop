<?php
session_start();
// Protects against Header Injection attack
// Progpilot indicates that it's still vulnerable, but this
// shouldn't be true. This is probably due to some of the
// limitations that progpilot has.
$redirects = ['/admin/create.php', '/admin/read.php', '/admin/update.php', '/admin/delete.php', '/admin/login.php?redirect=/admin/create.php', '/admin/login.php?redirect=/admin/read.php', '/admin/login.php?redirect=/admin/update.php', '/admin/login.php?redirect=/admin/delete.php'];
if(in_array($_SERVER['REQUEST_URI'], $redirects)) {
	if (!$_SESSION['username']) {
		header("Location: login.php?redirect={$_SERVER['REQUEST_URI']}");
		exit();
	}
} else {
	echo "An unexpected error has occured.";
	exit();
}
?>
