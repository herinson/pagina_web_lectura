<?php
session_start();

/**
 * Check if the user is authenticated.
 * If not, redirect to login page.
 */
function check_auth($pathToRoot = "") {
    if (!isset($_SESSION['user_id'])) {
        header("Location: " . $pathToRoot . "login.php");
        exit();
    }
}

/**
 * Check if the user has a specific role.
 * If not, redirect to dashboard or show error.
 */
function check_role($role, $pathToRoot = "") {
    check_auth($pathToRoot);
    if ($_SESSION['user_rol'] !== $role) {
        header("Location: " . $pathToRoot . "dashboard.php?error=unauthorized");
        exit();
    }
}
?>