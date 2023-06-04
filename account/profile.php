<?php

$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'myproject';
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}
// We don't have the first_name, Last_name, password, email or Profile Picture info stored in sessions, so instead, we can get the results from the database.
$stmt = $con->prepare('SELECT first_name, last_name, password, email, profile_pic FROM users WHERE id = ?');
// In this case we can use the account ID to get the account info.
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$stmt->bind_result($first_name, $last_name, $password, $email, $profile_pic);
$stmt->fetch();
$stmt->close();
?>

<?php include_once('upload.php') ?>

<?=template_header('Profile')?>

	<body class="loggedin">
		<div class="content">
				<h2>Profile Page</h2>
		
		<?php
		//This handles profile Picture Upload Response
		 if (!empty($statusMsg)): ?>
            <div class="alert <?php echo $statusMsg ?>" role="alert">
              <?php echo $statusMsg; ?>
            </div>
        <?php endif; ?>
		
			<div>
				<p>Your account details are below:</p>
				<table>
					<tr>
						<td> <img src="<?php echo 'uploads/' . $profile_pic ?>" width="90" height="90" alt=""> </td>
					</tr>
					
					<tr>
						<td>Username:</td>
						<td><?=$_SESSION['name']?></td>
					</tr>
                    <tr>
						<td>Name:</td>
						<td><?=$first_name?>  <?=$last_name?></td>
					</tr>
					<tr>
						<td>Password:</td>
						<td><i>Your Set Password</i></td>
					</tr>
					<tr>
						<td>Email:</td>
						<td><?=$email?></td>
					</tr>
				</table>
			</div>
			<form action="index.php?page=profile" method="post" enctype="multipart/form-data">
    			Select Image File to Upload As Your Profile Picture:
    			<input type="file" name="anyfile" id="anyfile">
    			<input type="submit" name="submit" value="Upload">
			</form>
		</div>
	</body>