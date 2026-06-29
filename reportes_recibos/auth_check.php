<?php
session_start();

/**
 * Check if the user is authenticated and manage session timeout.
 * If not, or if timed out, redirect to login page.
 */
function check_auth($pathToRoot = "") {
    if (!isset($_SESSION['user_id'])) {
        header("Location: " . $pathToRoot . "login.php");
        exit();
    }

    // Session timeout logic (30 minutes = 1800 seconds)
    $timeout_duration = 1800;
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout_duration)) {
        session_unset();
        session_destroy();
        header("Location: " . $pathToRoot . "login.php?timeout=1");
        exit();
    }
    $_SESSION['last_activity'] = time();
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