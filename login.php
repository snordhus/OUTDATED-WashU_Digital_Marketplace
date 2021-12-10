<?php

require 'database.php';

$email = mysql_escape_string($_GET['email']));
$password_guess = mysql_escape_string($_GET['password']);

//retrieve the user with the specified email to compare the passwords
$stmt = $mysqli->prepare("SELELCT password FROM users WHERE email=? ");

//print fail statement if query wasn't able to run
if(!$stmt1){
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
}

$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->bind_result($hashed_password);
$stmt->fetch();
$stmt->close();

if (password_verify($password_guess, $hashed_password)) {
    $_SESSION['email'] = $email;

    //creating token on login
	$_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));

    //redirect to the home page
    header("location: home.html");
}
elseif ($email != NULL || $pwd_guess != NULL) {
	printf("Email and/or password are incorrect");
}
else {
    printf("Email and/or password cannot be empty");
}