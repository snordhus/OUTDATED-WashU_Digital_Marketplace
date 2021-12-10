<?php

require 'database.php';

$username = mysql_escape_string($_POST['username']);
$Password = mysql_escape_string($_POST['password']);
$retype_password = mysql_escape_string($POST['retype_password']);
$email = mysql_escape_string($_POST['email']);
$duplicate_email = false;

if ($username != NULl || $password == NULL || $retype_password == NULL || $email == NULL) { //is frontend going to ensure inputs aren't empty?
    //ERROR MESSAGE NEED INPUT FOR ALL PARAMETERS
}
else if ($password != $retype_password) {
    //ERROR MESSAGE PASSWORDS DON'T MATCH
}
else {

    //retrieve already registered usernames to make sure there are no duplicates
	$stmt1 = $mysqli->prepare("select email from users");

	//print fail statement if query wasn't able to run
	if(!$stmt1){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}

	$stmt1->execute();
	$stmt1->bind_result($registered_emails);

	//checking if username is already taken or not
	while($stmt1->fetch() && $duplicate_email == false){
		if (strcmp($registered_emails, $email) == 0) {
			$user_taken = true;
		}
	}
	$stmt1->close();

    if ($duplicate_email == false) {

        //salt hashing password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $verified = false;

        //creating a hash that will be checked upon user verification
        $verification_hash = md5( rand(0,100000) );

        //inserting account info into database
        $stmt2 = $mysqli->prepare("INSERT into users (username, hashed_password, email, verified, verification_hash) VALUES (?, ?, ?, ?)")
        $stmt2->bind_param('sssss', $username, $hashed_password, $email, $verified, $verification_hash); //are booleans strings in mysql?
        $stmt2->execute();	
        $stmt2->close();
        
        //SET UP FOR SENDING VERIFICATION EMAIL
        $to      = $email; // Send email to our user
        $subject = 'WashU Digital Marketplace Email Verification'; // Give the email a subject 
        $message = '
    
        Thanks for signing up for WashU Digital Marketplace!
        Your account has been created, you can login after you have activated your account by pressing the url below.
    
        Please click this link to activate your account:
        http://www.yourwebsite.com/activate_account.php?email='.$email.'&verification_hash='.$verification_hash.'
    
        '; // Our message above including the link
                        
        $header = 'From:noreply@washudigitalmarketplace.com' . "\r\n"; // Set from headers

        mail($to, $subject, $message, $header); // Send our email

        header("location: email_verification_sent.html");
    }
    else {
        //looks like you already have an account mesage
    }
}
?>