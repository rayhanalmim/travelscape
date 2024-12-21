<?php
if (isset($_POST["submit"])) {
    $email = $_POST["email"];
    $username = $_POST["uid"];
    $pwd = $_POST["pwd"];
    $pwdconfirm = $_POST["pwdconfirm"];

    require_once 'dbh-inc.php';
    require_once 'functions-inc.php';

    // Validate inputs
    if (emptyInputSignup($email, $username, $pwd, $pwdconfirm)) {
        header("location: login.php?error=emptyinput");
        exit();
    }
    if (invalidUid($username)) {
        header("location: login.php?error=invaliduid");
        exit();
    }
    if (invalidEmail($email)) {
        header("location: login.php?error=invalidemail");
        exit();
    }
    if (pwdMatch($pwd, $pwdconfirm)) {
        header("location: login.php?error=passwordsdontmatch");
        exit();
    }
    if (uidExists($conn, $username, $email)) {
        header("location: login.php?error=usernametaken");
        exit();
    }

    // Create the user
    createUser($conn, $email, $username, $pwd);
} else {
    header("location: login.php");
    exit();
}
