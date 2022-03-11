<?php

// Developer note:
// Q: Why is the db.inc.php file copied here?
// A: In the original cart code that was given, it made use
// of a variable called $_SERVER['DOCUMENT_ROOT'], inclusion
// of this variable in the code could allow an attack to exploit
// a Local File Inclusion (LFI) vulnerability. Removing that and
// copying the db.inc.php file into here prevents that.

$mysqli = new mysqli("127.0.0.1", "terry", "P@ssw0rd123", "discojuice", 3306);

?>
