<?php
// Change this to your connection info.
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'myproject';
// Try and connect using the info above.
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
	// If there is an error with the connection, stop the script and display the error.
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

// Now we check if the data was submitted, isset() function will check if the data exists.
if (!isset($_POST['first_name'], $_POST['last_name'], $_POST['username'], $_POST['password'], $_POST['email'])) {
	// Could not get the data that should have been sent.
	$msg = "Please complete the registration form!";
	header("Location:http://localhost/project/register.php?msg=$msg");
	exit();
}
// Make sure the submitted registration values are not empty.
if (empty($_POST['first_name']) || empty($_POST['last_name']) || empty($_POST['username']) || empty($_POST['password']) || empty($_POST['email'])) {
	// One or more values are empty.
	$msg = "Please complete the registration form!";
	header("Location:http://localhost/project/register.php?msg=$msg");
	exit();
}

// We need to do series of User Input Validations.
//Email
if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
	$msg = "Email is not valid!";
	header("Location:http://localhost/project/register.php?msg=$msg");
	exit();
}

//Valid Input as Username
if (preg_match('/^[a-zA-Z0-9]+$/', $_POST['username']) == 0) {
	$msg = "Username is not valid!";
	header("Location:http://localhost/project/register.php?msg=$msg");
    exit();
}

//Password Length
if (strlen($_POST['password']) > 20 || strlen($_POST['password']) < 5) {
	$msg = "Password must be between 5 and 20 characters long!";
	header("Location:http://localhost/project/register.php?msg=$msg");
	exit();
}

// We need to check if the account with that username exists.
if ($stmt = $con->prepare('SELECT id, password FROM users WHERE username = ?')) {
	// Bind parameters (s = string, i = int, b = blob, etc), hash the password using the PHP password_hash function.
	$stmt->bind_param('s', $_POST['username']);
	$stmt->execute();
	$stmt->store_result();
	// Store the result so we can check if the account exists in the database.
	if ($stmt->num_rows > 0) {
		// Username already exists
		$msg = "Username exists, please choose another!.";
		header("Location:http://localhost/project/register.php?msg=$msg");
	} else {
	// Username doesn't exists, insert new account
if ($stmt = $con->prepare('INSERT INTO users (first_name, last_name, username, password, email, activation_code) VALUES (?, ?, ?, ?, ?, ?)')) {
	// We do not want to expose passwords in our database, so hash the password and use password_verify when a user logs in.
	$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
	$uniqid = uniqid();
	$stmt->bind_param('ssssss', $_POST['first_name'], $_POST['last_name'], $_POST['username'], $password, $_POST['email'], $uniqid);
	$stmt->execute();
	
//Email Account Confirmation
	$from    = 'noreply@yourdomain.com';
	$subject = 'Account Activation Required';
	$headers = 'From: ' . $from . "\r\n" . 'Reply-To: ' . $from . "\r\n" . 'X-Mailer: PHP/' . phpversion() . "\r\n" . 'MIME-Version: 1.0' . "\r\n" . 'Content-Type: text/html; charset=UTF-8' . "\r\n";
// Update the activation variable below
$activate_link = 'http://localhost/project/auth/activate.php?email=' . $_POST['email'] . '&code=' . $uniqid;
$message = '<p>Please click the following link to activate your account: <a href="' . $activate_link . '">' . $activate_link . '</a></p>';
mail($_POST['email'], $subject, $message, $headers);
$msg = "Please check your email to activate your account!";
header("Location:http://localhost/project/register.php?msg=$msg");
} else {
	// Something is wrong with the SQL statement, so you must check to make sure your accounts table exists with all 3 fields.
	$msg = "Could not prepare statement!";
	header("Location:http://localhost/project/register.php?msg=$msg");
}
	}
	$stmt->close();
} else {
	// Something is wrong with the SQL statement, so you must check to make sure your accounts table exists with all 3 fields.
	$msg = "Could not prepare statement!";
	header("Location:http://localhost/project/register.php?msg=$msg");
}
$con->close();
?>