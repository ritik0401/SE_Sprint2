<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <link rel="stylesheet" type="text/css" href="forgot_password.css"> 
</head>
<body>
    <div class="login-container">
        <h2>Forgot Your Password?</h2>
        <p>Enter your email address and we'll send you a link to set your password.</p>

        <form action="forgot_password.php" method="POST">
            <div class="form-group">
                <div class="registration-container">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" placeholder="Enter email" required>
                </div>
            </div>
            
            <input type="submit" value="Send">
        </form>

        <p>Know your password? <a href="login.html">Sign in</a></p>
    </div>
</body>
</html>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    echo "If the email address exists in our database, a password reset link will be sent.";
}
?>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    $dbHost = "your_database_host";
    $dbUsername = "your_database_username";
    $dbPassword = "your_database_password";
    $dbName = "your_database_name";

    $conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $resetLink = "https://example.com/reset_password.php"; 
        $subject = "Password Reset Link";
        $message = "Click the following link to reset your password: $resetLink";
        mail($email, $subject, $message);

        echo "If the email address exists in our database, a password reset link will be sent.";
        
        header("Location: login.html");
        exit();
    } else {
        echo "Email not found in our database. Please check the email address.";
    }

    $conn->close();
}
?>
