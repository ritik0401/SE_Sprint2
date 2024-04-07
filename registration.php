
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Registration</title>
    <link rel="stylesheet" href="login.css"> 
    <script>
        function validateForm() {
            var firstName = document.getElementById("firstName").value;
            var lastName = document.getElementById("lastName").value;
            var email = document.getElementById("email").value;
            var username = document.getElementById("username").value;
            var password = document.getElementById("password").value;
            var confirmPassword = document.getElementById("confirmPassword").value;
            var errorMessages = [];

            if (firstName === "") {
                errorMessages.push("First Name is required");
            }
            if (lastName === "") {
                errorMessages.push("Last Name is required");
            }
            if (email === "") {
                errorMessages.push("Email is required");
            } else {
                var emailRegex = /\S+@\S+\.\S+/;
                if (!emailRegex.test(email)) {
                    errorMessages.push("Enter a valid email address");
                }
            }
            if (username === "") {
                errorMessages.push("Username is required");
            }
            if (password === "") {
                errorMessages.push("Password is required");
            } else if (password.length < 6) {
                errorMessages.push("Password should be at least 6 characters long");
            }
            if (confirmPassword === "") {
                errorMessages.push("Please confirm your password");
            } else if (confirmPassword !== password) {
                errorMessages.push("Passwords do not match");
            }

            var errorContainer = document.getElementById("errorContainer");
            errorContainer.innerHTML = "";
            if (errorMessages.length > 0) {
                for (var i = 0; i < errorMessages.length; i++) {
                    var errorMessage = document.createElement("p");
                    errorMessage.textContent = errorMessages[i];
                    errorContainer.appendChild(errorMessage);
                }
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $host = "localhost";
        $user = "rpatel245";
        $pass = "rpatel245";
        $dbname = "rpatel245";

        $conn = new mysqli($host, $user, $pass);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql_create_db = "CREATE DATABASE IF NOT EXISTS rpatel245";
        if ($conn->query($sql_create_db) === TRUE) {
            $conn->close();
            $conn = new mysqli($host, $user, $pass, $dbname);

            $sql_create_users_table = "CREATE TABLE IF NOT EXISTS users (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                first_name VARCHAR(30) NOT NULL,
                last_name VARCHAR(30) NOT NULL,
                email VARCHAR(50) NOT NULL,
                username VARCHAR(30) NOT NULL,
                password VARCHAR(255) NOT NULL,
                reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";

            if ($conn->query($sql_create_users_table) !== TRUE) {
                echo "Error creating users table: " . $conn->error;
            }

            $firstName = $_POST['firstName'];
            $lastName = $_POST['lastName'];
            $email = $_POST['email'];
            $username = $_POST['username'];
            $password = $_POST['password'];
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $sql_insert_user = "INSERT INTO users (first_name, last_name, email, username, password)
            VALUES (?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql_insert_user);
            $stmt->bind_param("sssss", $firstName, $lastName, $email, $username, $hashedPassword);

            if ($stmt->execute()) {
                echo "Registration successful! <a href='login.php'> Login Here!</a> ";
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error creating database: " . $conn->error;
        }
        $conn->close();
    }
    ?>

    <div class="container"> 
        <h2 class="welcome-title">User Registration</h2> 
        <form method="POST" onsubmit="return validateForm()" class="form-box"> 
            <div class="input-box">
                <label for="firstName" class="input-label">First Name:</label>
                <input type="text" id="firstName" name="firstName" placeholder="Enter first name" required class="input-field">
            </div>
            <div class="input-box">
                <label for="lastName" class="input-label">Last Name:</label>
                <input type="text" id="lastName" name="lastName" placeholder="Enter last name" required class="input-field">
            </div>
            <div class="input-box">
                <label for="email" class="input-label">Email:</label>
                <input type="email" id="email" name="email" placeholder="Enter email" required class="input-field">
            </div>
            <div class="input-box">
                <label for="username" class="input-label">Username:</label>
                <input type="text" id="username" name="username" placeholder="Choose a username" required class="input-field">
            </div>
            <div class="input-box">
                <label for="password" class="input-label">Password:</label>
                <input type="password" id="password" name="password" placeholder="Create a password" required class="input-field">
            </div>
            <div class="input-box">
                <label for="confirmPassword" class="input-label">Confirm Password:</label>
                <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm your password" required class="input-field">
            </div>
            <button type="submit" class="submit-btn">Register</button>
        </form>
        <div id="errorContainer"></div> 
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
