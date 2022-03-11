<?php
// Start the user's session
session_start();

// Required our database connection
// Modified from original $_SERVER['DOCUMENT_ROOT'] value to
// prevent LFI vulnerability. More information located
// in the comments of db.inc.php
require_once "db.inc.php";

// Check that there are contents in the cart, otherwise redirect back to show the empty cart message
if(empty($_SESSION['cart'])) {
	header("Location: /cart/");
	exit();
}


// Form variables
$myname = $_REQUEST['name'];
$mystreet = $_REQUEST['street'];
$mycity = $_REQUEST['city'];
$mystate = $_REQUEST['state'];
$myzip = $_REQUEST['zip'];
$mycreditcard = $_REQUEST['creditcard'];
$myexpiration = $_REQUEST['expiration'];
$mysecuritycode = $_REQUEST['securitycode'];

?>
<!DOCTYPE HTML>
<html lang=en>

<head>
	<title>Disco Juice - Checkout</title>
	<style>
		.error {
			border: 1px solid red;
			color: red;
			padding: .5rem;
			width: 50rem;
		}
		th {
			text-align: right;
			color: white;
		}
		body {
			background: linear-gradient(to right, #292560, #410141);
			min-height: 100vh;
		}
		h1 {
			color: white;
		}
		p {
			color: white;
		}
	</style>
</head>

<body>
<h1>Checkout</h1>

<?php

//BEGIN: If-else field check
// If ALL of the fields have been submitted, enter the order
if (!empty($myname) && !empty($mystreet) && !empty($mycity) && !empty($myzip) && !empty($mycreditcard) && !empty($myexpiration) && !empty($mysecuritycode) && $_REQUEST['csrf_token'] === $_SESSION['csrf_token']) {
	unset($SESSION['csrf_token']);
	//Sanitize every variable to prevent XSS,
	//incase something slips past the original form restrictions below
	$myname = htmlentities($myname);
	$mystreet = htmlentities($mystreet);
	$mycity = htmlentities($mycity);
	$myzip = htmlentities($myzip);
	$mycreditcard = htmlentities($mycreditcard);
	$myexpiration = htmlentities($myexperiation);
	$mysecuritycode = htmlentities($mysecuritycode);
	
	// Insert the order into the database
	$stmt = mysqli_prepare($mysqli, "INSERT INTO orders (name, street, city, state, zip, creditcard, expiration, securitycode) VALUES (?, ?, ?, ?, ?, ?, ?, ?)") or die('Error in SQL: ' . mysqli_error($mysqli));
	mysqli_stmt_bind_param($stmt, "ssssssss", $myname, $mystreet, $mycity, $mystate, $myzip, $mycreditcard, $myexpiration, $mysecuritycode);
	mysqli_stmt_execute($stmt);
	$results = mysqli_stmt_get_result($stmt);
	$order_id = mysqli_insert_id($mysqli);

	// Loop through the items in the shopping cart
	foreach($_SESSION['cart'] as $item_product_id => $item) {
		foreach($item as $item_price => $item_quantity) {
			$shopping_cart_total += $item_quantity * $item_price;

			// Foreach product ordered, add the product id, quantity, and price
			$stmt = mysqli_prepare($mysqli, "INSERT INTO line_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
			mysqli_stmt_bind_param($stmt, "iiii", $order_id, $item_product_id, $item_quantity, $item_price);
			mysqli_stmt_execute($stmt);
		}
	}

	// Now that everything is entered into the database, empty the cart
	unset($_SESSION['cart']);
?>

	<p>Thank you for your order! Your order confirmation number is <strong><?= $order_id ?></strong>, and you have been charged <strong>$<?= number_format($shopping_cart_total,2) ?></strong>. Please allow 5-30 business days to receive it in the post.</p>
	<p><em>Just when you've forgotten about it, or decide you want a refund, it'll show up for sure! (Or just wait another day or two...)</em></p>

<?php
// Else not ALL of the fields have been submitted, so show the form
} else {

// If one or more of the fields have been submitted, display an error message
if (isset($myname) || isset($mystreet) || isset($mycity) || isset($myzip) || isset($mycreditcard) || isset($myexpiration) || isset($mysecuritycode)) {
	echo "<p class='error'>ERROR: Please complete all fields.</p>";

}

// Set CSRF Token
$_SESSION['csrf_token'] = bin2hex(random_bytes(64));

?>

<p>Please enter your billing details.</p>
<form method="POST">
	<input type='hidden' name='csrf_token' value='<?= $_SESSION['csrf_token'] ?>'>
	<table>
		<tr>
			<th><label for="name">Name</label></th>
			<td><input id="name" type="text" name="name" value="<?= $myname ?>" required /></td>
		</tr>
		<tr>
			<th><label for="street">Street</label></th>
			<td><input id="street" type="text" name="street" value="<?= $mystreet ?>" pattern='[a-zA-Z0-9 ]+' required /></td>
		</tr>
		<tr>
			<th><label for="city">City</label></th>
			<td><input id="city" type="text" name="city" value="<?= $mycity ?>" pattern='[a-zA-Z]+[a-zA-Z ]+' required /></td>
		</tr>
		<tr>
			<th><label for="state">State</label></th>
			<td><select id="state" name="state">
				<option></option>

<?php

$states = array(
	'AL'=>'Alabama',
	'AK'=>'Alaska',
	'AZ'=>'Arizona',
	'AR'=>'Arkansas',
	'CA'=>'California',
	'CO'=>'Colorado',
	'CT'=>'Connecticut',
	'DE'=>'Delaware',
	'DC'=>'District of Columbia',
	'FL'=>'Florida',
	'GA'=>'Georgia',
	'HI'=>'Hawaii',
	'ID'=>'Idaho',
	'IL'=>'Illinois',
	'IN'=>'Indiana',
	'IA'=>'Iowa',
	'KS'=>'Kansas',
	'KY'=>'Kentucky',
	'LA'=>'Louisiana',
	'ME'=>'Maine',
	'MD'=>'Maryland',
	'MA'=>'Massachusetts',
	'MI'=>'Michigan',
	'MN'=>'Minnesota',
	'MS'=>'Mississippi',
	'MO'=>'Missouri',
	'MT'=>'Montana',
	'NE'=>'Nebraska',
	'NV'=>'Nevada',
	'NH'=>'New Hampshire',
	'NJ'=>'New Jersey',
	'NM'=>'New Mexico',
	'NY'=>'New York',
	'NC'=>'North Carolina',
	'ND'=>'North Dakota',
	'OH'=>'Ohio',
	'OK'=>'Oklahoma',
	'OR'=>'Oregon',
	'PA'=>'Pennsylvania',
	'RI'=>'Rhode Island',
	'SC'=>'South Carolina',
	'SD'=>'South Dakota',
	'TN'=>'Tennessee',
	'TX'=>'Texas',
	'UT'=>'Utah',
	'VT'=>'Vermont',
	'VA'=>'Virginia',
	'WA'=>'Washington',
	'WV'=>'West Virginia',
	'WI'=>'Wisconsin',
	'WY'=>'Wyoming',
);


foreach($states as $key => $value)
	echo "<option value='$key'".($mystate==$key ? " selected" : "").">$value</option>\n";
?>

			</select>				
		</tr>
		<tr>
			<th><label for="zip">Zip</label></th>
			<td><input id="zip" type="text" name="zip" value="<?= $myzip ?>" maxlength='5' required /></td>
		</tr>
		<tr>
			<th><label for="creditcard">Credit Card</label></th>
			<td><input id="creditcard" type="text" name="creditcard" value="<?= $mycreditcard ?>" pattern='[0-9]+' min='16'  maxlength='16' required /></td>
		</tr>
		<tr>
			<th><label for="expiration">Expiration</label></th>
			<td><input id="expiration" type="month" name="expiration" value="<?= $myexpiration ?>" pattern='[0-9][0-9][0-9][0-9]' maxlength='4' required /></td>
		</tr>
		<tr>
			<th><label for="securitycode">Security Code</label></th>
			<td><input id="securitycode" type="password" name="securitycode" min='3' maxlength="4" value="<?= $mysecuritycode ?>" pattern='[0-9]+' required /></td>

		</tr>
		<tr>
			<td></td>
			<td><input type="submit" value="Complete Purchase" /></td>
		</tr>
	</table>
</form>

<?php
} // END: If-else field check
?>
</body>
</html>
