<?php
  
require_once "db.inc.php";
session_start();
// Set CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(64));

?>
<html>

<head>
    <title>Discount Juice Shop</title>
<style>
    body {
        padding-top: 50px;
        padding-right: 150px;
        padding-left: 150px;
	    padding-bottom: 25px;
	    color: white;
        background: linear-gradient(to right, #292560, #410141);
        min-height: 100vh;
    }
	p {
	    color: white;
	}
    h1{
        color: white;
    }
    h2 {
        color: white;
    }
	h4{
	    color: white;
	}
	h5{
	    color: white;
	}
</style>
</head>

<body>
<center>

    <h1>Welcome to Discount Juice Shop!</h1>
    <h2>Checkout our products below!</h2>
    <img src="line.png" width="200px"><br>

<form>
<input type="text" name="search">
<input type="submit" value="Search">
</form>

<?php

$mysearch = $_REQUEST['search'];
$mysearchextended = '%' . $mysearch . '%';

$stmt = mysqli_prepare($mysqli, "SELECT * FROM products WHERE name LIKE ? ORDER BY name") or die('Error in SQL: ' . mysqli_error($mysqli));
mysqli_stmt_bind_param($stmt, "s", $mysearchextended);
mysqli_stmt_execute($stmt);
$results = mysqli_stmt_get_result($stmt);

// Clears search bar so user doesn't just see:
// "Results for: "
if ($mysearch == null) {
	echo "";
} else {
	echo "<p>Results for: " . htmlentities($mysearch) . "</p>";
}

while($row = mysqli_fetch_array($results)) {
	// Sanitization to prevent XSS
	$name = htmlentities($row['name']);
	$price = htmlentities($row['price']);
	$calories = htmlentities($row['calories']);
	$juicepercent = htmlentities($row['juicepercent']);
	$id = htmlentities($row['id']);
	echo "<h4>{$name}</h4><h5>Price: \${$price}<br>Calories: {$calories} cal<br>Real Juice Contents: {$juicepercent}%</h5>";

?>
<form action="/cart/" method="POST">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
<input type="hidden" name="product_id" value="<?= $id ?>">
<input type="number" name="quantity" value="1" min="1" max="50">
<input type="submit" value="Add to Cart">
</form>
<br><br> 
<?php
} // End of while() loop
?>
    
    <a href="/"><img height="125px" width="275px" src="GoBack.gif"></a>

</center>
</body>

</html>
