<?php

require_once("classes/User.php");
session_start();

if (isset($_SESSION['user']) && $_SESSION['user'] instanceof User) {
    $user = $_SESSION['user'];
    if ($user->logout()) {
        // Logout successful, redirect to homepage
        header("Location: index.php");
        exit();
    } else {
        // Logout failed, handle error
        echo "<script>alert('Logout failed. Please try again.'); window.location.href='index.php';</script>";
    }
} else {
    // No user is logged in, redirect to homepage
    header("Location: index.php");
    exit();
}

?>
