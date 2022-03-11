<?php
// Start the user's session
session_start();

// Required our database connection
require_once "db.inc.php";

$myproduct_id = $_REQUEST['product_id'];

// Set quantity value to 1 if not specified
if ($_REQUEST['quantity'] && $_REQUEST['quantity']>1) {
	$myquantity = $_REQUEST['quantity'];
} else {
	$myquantity = 1;
}

$myremove_product_id = $_REQUEST['remove_product_id'];

// If the user requested an item to be removed, remove it
if(!empty($myremove_product_id)) {
	unset($_SESSION['cart'][$myremove_product_id]);
}

// If the user sent a product_id, add the quantity to the existing cart quantity
if(!empty($myproduct_id) && $_REQUEST['csrf_token']===$_SESSION['csrf_token']) {

	// Reset the token so it cannot be used twice
	unset($_SESSION['csrf_token']);

	//Prepare query
	$stmt = mysqli_prepare($mysqli, "SELECT price FROM products WHERE id = ?") or die('Error in SQL: ' . mysqli_error($mysqli));
	mysqli_stmt_bind_param($stmt, "i", $myproduct_id);
	
	//Run query & send data
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	
	//Get results of a single row
	$row = mysqli_fetch_array($result);
	$myprice = $row['price'];

	$_SESSION['cart'][$myproduct_id][$myprice] += $myquantity;
}

$stmt = mysqli_prepare($mysqli, "SELECT * FROM products");
mysqli_stmt_execute($stmt);
$results = mysqli_stmt_get_result($stmt);

while($row = mysqli_fetch_array($results)) {
	// This will produce an array where the product id shows the name; for example:
	// $product_name[1] = 'Apple Juice'
	// $product_name[2] = 'Orange Juice'
	$product_name[$row['id']] = $row['name'];
}

?>
<!DOCTYPE HTML>
<html lang=en>

<head>
	<title>Disco Juice - Shopping Cart</title>
	<style>
		table {
			border-collapse: collapse;
			width: 50rem;
		}
		td, th {
			border: 1px solid white;
			padding: .5rem;
			color: white;
		}
		th {
			text-align: left;
			color: white;
		}
		td.price, th.price {
			text-align: right;
		}
		.remove {
			text-decoration: none;
		}
		body {
			background: linear-gradient(to right, #292560, #410141);
			min-height: 100vh;
		}
		h1{
			color: white;
		}
		p{
			color: white;
		}
		a {
			color: white;
		}
		a:hover {
			color: #292560;
		}
		a:visited {
			text-decoration: none;
			color: white;
		}	
	</style>
</head>

<body>
<h1>Shopping Cart</h1>

<?php
// BEGIN: If-else shopping cart check
// If the shopping cart is empty, tell the user
if(empty($_SESSION['cart'])) {
	echo "<p>Your shopping cart is empty.</p>";

// Else show the cart contents
} else {
?>

<p>You've picked out some great products! Ready to check out?</p>
<table>
	<thead>
		<tr><th>Product</th><th class="price">Quantity @ Price</th><th class="price">Subtotal</th></tr>
	</thead>
	<tbody>

<?php

// Loop through the items in the shopping cart
foreach($_SESSION['cart'] as $item_product_id => $item) {
	foreach($item as $item_price => $item_quantity) {
		// Find the item name based on our previous database query
		$item_name = $product_name[$item_product_id];
		$item_subtotal = $item_quantity * $item_price;
		$shopping_cart_total += $item_subtotal;
		$shopping_cart_total = htmlentities($shopping_cart_total);

		// Sanitize variables to protect against XSS
		$item_name = htmlentities($item_name);
		$item_product_id = htmlentities($item_product_id);
		$item_quantity = htmlentities($item_quantity);
		$item_subtotal = htmlentities($item_subtotal);

		// Display the table row with a subtotal
		echo "<tr><td>$item_name <a class='remove' href='?remove_product_id=$item_product_id' onclick='return confirm(\"Remove from cart?\");'>&#x1f5d1;</a></td><td class='price'>$item_quantity @ $".number_format($item_price,2)."</td><td class='price'>$".number_format($item_subtotal,2)."</td></tr>";

	}
}
?>

	</tbody>
	<tfoot>
		<tr><th colspan="2" class="price">TOTAL</th><td class="price">$<?= number_format($shopping_cart_total,2) ?></td></tr>
	</tfoot>
</table>

<?php
//END: If-else shopping cart check
}
?>

<p><a href="<?= $_SERVER['HTTP_REFERER'] && (parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH) != parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)) ? $_SERVER['HTTP_REFERER'] : "/Products.php" ?>">Continue Shopping</a>

<?php
// Only show the "Checkout" button if there are contents in the cart
if(!empty($_SESSION['cart'])) {
?>

or <button onclick="document.location='checkout.php'">Checkout</button>

<?php
}
?>

</p>
</body>
</html>
