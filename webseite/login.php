<?php
require_once("classes/User.php");

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // $testuser = User::createUser("MAX", "password");

    // Try to retrieve the user from the username
    $user = User::getFromUsername($username);

    if ($user && $user->login($password)) {
        // Redirect to a protected page if login is successful
        header("Location: index.php?login=success");

    } else {
        // Redirect back to the login page with an error message
        header("Location: index.php?error=1");
    }
}
?>