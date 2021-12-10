<?php

require 'database.php';

$email = mysql_escape_string($_GET['email']));
$hash = mysql_escape_string($_GET['hash']);
$confirmed = false;
$message;

//retrieve already registered usernames to make sure there are no duplicates
$stmt1 = $mysqli->prepare("SELELCT hash FROM users WHERE email=? ");

//print fail statement if query wasn't able to run
if(!$stmt1){
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
}

$stmt1->bind_param('s', $email);
$stmt1->execute();
$stmt1->bind_result($hash_database);
$stmt1->fetch();
$stmt1->close();

//checking if username is already taken or not
if (strcmp($hash, $hash_database) == 0) {

    $verified = true;

    $stmt2 = $mysqli->prepare("INSERT into users (verified) VALUES (?)")
    $stmt2->bind_param('s', $verified); //are booleans strings in mysql?
    $stmt2->execute();	
    $stmt2->close();

    $message = '
 
    Your account has been activated for the WashU Digital Marketplace!
 
    You can log in here:
    http://www.yourwebsite.com/login.html
 
    '; // Our message above including the link
}
else {
    $message = '
 
    An error occured and your account was not activated for the WashU Digital Marketplace.

    Please ensure that you use the exact link provided in the email verification email.
 
    '; // Our message above including the link
}

 //SENDING CONFIRMATION EMAIL THAT ACCOUNT HAS BEEN CREATED
 $to      = $email; // Send email to our user
 $subject = 'WashU Digital Marketplace Account Activation'; // Give the email a subject 
                     
 $header = 'From:noreply@washudigitalmarketplace.com' . "\r\n"; // Set from headers

 mail($to, $subject, $message, $header); // Send our email
?>