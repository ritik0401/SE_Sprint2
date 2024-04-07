<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome to Scraps</title>
    <link rel="stylesheet" href="login.css">
    <script>
        function showInvalidLoginMessage() {
            alert("Invalid username or password!");
        }
    </script>
</head>
<body>
    <?php
    session_start();

    $loginAttempted = false;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $host = "localhost";
        $user = "rpatel245";
        $pass = "rpatel245";
        $dbname = "rpatel245";

        $conn = new mysqli($host, $user, $pass, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $username = $_POST['username'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT password FROM users WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                $_SESSION['username'] = $username;
                header("Location: recipe.php");
                exit();
            }
        }
        $loginAttempted = true;

        $conn->close();
    }

    if ($loginAttempted) {
        echo '<script type="text/javascript">showInvalidLoginMessage();</script>';
    }
    ?>

    <div class="container">
        <h2 class="welcome-title">Welcome to Scraps</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST" class="form-box">
            <div class="input-box">
                <label for="username" class="input-label">Username:</label>
                <input type="text" id="username" name="username" placeholder="Enter Username" required class="input-field">
            </div>
            <div class="input-box">
                <label for="password" class="input-label">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter password" required class="input-field">
            </div>
            <button type="submit" class="submit-btn">Sign In</button>
            <p class="forgot-password"><a href="forgot_password.php">Forgot your password?</a></p>
            <p class="register-link">Don't have an account? <a href="registration.php">Create a New Account</a></p>
        </form>
    </div>

    <script>
const formBox = document.querySelector('.form-box');
const inputField = document.querySelectorAll('.input-field');

formBox.addEventListener('animationend', () => {
  inputField[0].focus();
});
    </script>
</body>
</html>