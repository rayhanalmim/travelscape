<?php
        if (isset($_GET["error"])) {
            if ($_GET["error"] == "emptyinput"){
                echo "<p>Fill in all fields!</p>";
            }
            else if($_GET["error"] == "invaliduid"){
                echo "<p>Choose a proper username</p>";
            }
            else if($_GET["error"] == "invaliduemail"){
                echo "<p>Choose a proper email/p>";
            }
            else if($_GET["error"] == "usernametaken"){
                echo "<p>Passwords don't match!</p>";
            }
            else if($_GET["error"] == "stmtfailed"){
                echo "<p>Something went wrong! Try again!</p>";
            }
            else if($_GET["error"] == "usernametaken"){
                echo "<p>Try another username</p>";
            }
            else if ($_GET["error"] == "none"){
                echo "<p>Successfully Signed Up!</p>";
            }
        }
        
        ?>

<!DOCTYPE html>
<?php
session_start();
$mysqli = new mysqli("localhost", "root", "", "travelscapes");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["submit"])) {
        // Sign-up logic
        $email = $_POST["email"];
        $username = $_POST["uid"];
        $password = $_POST["pwd"];
        $confirmPassword = $_POST["pwdconfirm"];

        if (empty($email) || empty($username) || empty($password) || empty($confirmPassword)) {
            $signupError = "Please fill in all fields.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $signupError = "Invalid email format.";
        } elseif ($password !== $confirmPassword) {
            $signupError = "Passwords do not match.";
        } else {
            $query = "SELECT * FROM login WHERE usersuid = ? OR usersEmail = ?";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                $signupError = "User already exists.";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $query = "INSERT INTO login (usersid, usersEmail, usersuid, userspwd) VALUES (NULL, ?, ?, ?)";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param("sss", $email, $username, $hashedPassword);
                if ($stmt->execute()) {
                    header("Location: loggedinhome.php");
                    exit();
                } else {
                    $signupError = "Error creating account.";
                }
            }
        }
    }

    if (isset($_POST["login"])) {
        // Login logic
        $usernameOrEmail = $_POST["uid"];
        $password = $_POST["pwd"];

        if (empty($usernameOrEmail) || empty($password)) {
            $loginError = "Please fill in all fields.";
        } else {
            $query = "SELECT * FROM login WHERE usersuid = ? OR usersEmail = ?";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("ss", $usernameOrEmail, $usernameOrEmail);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                if (password_verify($password, $row["userspwd"])) {
                    $_SESSION["username"] = $row["usersuid"];
                    header("Location: loggedinhome.php");
                    exit();
                } else {
                    $loginError = "Incorrect password.";
                }
            } else {
                $loginError = "User not found.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Dual Login / Signup Form</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width" />
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.0.2/css/bootstrap.min.css'>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css'>
    <link rel="stylesheet" href="../css/login.css" />
</head>

<body>
    <section>
        <div class="container" id="container">
            <div class="form-container sign-up-container">
                <form method="POST">
                    <h1>Sign Up</h1>
                    <div class="social-container">
                        <a href="https://github.com/rayhanalmim" class="social"><i class="fab fa-github"></i></a>
                    </div>
                    <span>Or use your Email for registration</span>
                    <?php if (isset($signupError)) echo "<p class='text-danger'>$signupError</p>"; ?>
                    <?php if (isset($signupSuccess)) echo "<p class='text-success'>$signupSuccess</p>"; ?>
                    <label>
                        <input type="email" name="email" placeholder="Email" />
                    </label>
                    <label>
                        <input type="text" name="uid" placeholder="Username" />
                    </label>
                    <label>
                        <input type="password" name="pwd" placeholder="Password" />
                    </label>
                    <label>
                        <input type="password" name="pwdconfirm" placeholder="Confirm Password" />
                    </label>
                    <button type="submit" name="submit">Sign Up</button>
                </form>
            </div>

            <div class="form-container sign-in-container">
                <form method="POST">
                    <h1>Login</h1>
                    <div class="social-container">
                        <a href="https://github.com/rayhanalmim" class="social"><i class="fab fa-github"></i></a>
                    </div>
                    <?php if (isset($loginError)) echo "<p class='text-danger'>$loginError</p>"; ?>
                    <?php if (isset($loginSuccess)) echo "<p class='text-success'>$loginSuccess</p>"; ?>
                    <label>
                        <input type="text" name="uid" placeholder="Username or Email" />
                    </label>
                    <label>
                        <input type="password" name="pwd" placeholder="Password" />
                    </label>
                    <a href="#">Forgot your password?</a>
                    <button name="login">Login</button>
                </form>
            </div>

            <div class="overlay-container">
                <div class="overlay">
                    <div class="overlay-panel overlay-left">
                        <h1>Welcome Back!</h1>
                        <p>Login here if you already have an account</p>
                        <button class="ghost" id="signIn">Login</button>
                    </div>
                    <div class="overlay-panel overlay-right">
                        <h1>Create an Account!</h1>
                        <p>Sign up if you still don't have an account...</p>
                        <button class="ghost" id="signUp">Sign Up</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        const signUpButton = document.getElementById('signUp');
        const signInButton = document.getElementById('signIn');
        const container = document.getElementById('container');

        signUpButton.addEventListener('click', () =>
            container.classList.add('right-panel-active'));

        signInButton.addEventListener('click', () =>
            container.classList.remove('right-panel-active'));
    </script>
</body>

</html>
