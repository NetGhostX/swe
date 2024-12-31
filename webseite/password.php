<?php
require_once("classes/User.php");

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user']) || !$_SESSION['user']->isLoggedIn()) {
        header("Location: index.php?password_error=not_logged_in");
        exit();
    }

    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmNewPassword = $_POST['confirmNewPassword'];

    $user = $_SESSION['user'];

    // Prüfe, ob das aktuelle Passwort korrekt ist
    if (!$user->isPasswordCorrect($currentPassword)) {
        header("Location: index.php?password_error=wrong_current_password");
        exit();
    }

    // Prüfe, ob die neuen Passwörter übereinstimmen
    if ($newPassword !== $confirmNewPassword) {
        header("Location: index.php?password_error=password_mismatch");
        exit();
    }

    // Aktualisiere das Passwort
    if ($user->changePassword($currentPassword, $newPassword)) {
        header("Location: index.php?password_success=1");
        exit();
    } else {
        header("Location: index.php?password_error=update_failed");
        exit();
    }
}
?>