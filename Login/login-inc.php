<?php
if (isset($_POST["login"])) {
    $username = $_POST["uid"];
    $pwd = $_POST["pwd"];

    require_once 'dbh-inc.php';
    require_once 'functions-inc.php';

    // Validate inputs
    if (emptyInputLogin($username, $pwd)) {
        header("location: login.php?error=emptyinput");
        exit();
    }

    // Log in the user
    loginUser($conn, $username, $pwd);
} else {
    header("location: login.php");
    exit();
}
