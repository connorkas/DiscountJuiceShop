<?php

// Starts and immediately kills session
// to wipe all browser-session data
session_start();
session_unset();
session_destroy();

?>
<html>
<body>

<h2>You have been logged out.</h2>

<h3><a href="/admin/login.php">Login</a></h3>

</body>
</html>
