<?php
session_start();

// Check if the user is logged in.
if (!isset($_SESSION['username'])) {
    echo "You need to log in to delete your account.";
    exit();
}

$host = "localhost";
$user = "rpatel245";
$pass = "rpatel245";
$dbname = "rpatel245";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION['username'];

$stmt = $conn->prepare("DELETE FROM users WHERE username=?");
$stmt->bind_param("s", $username);

if ($stmt->execute() === TRUE) {
    echo "Account deleted successfully.";

    session_unset();
    session_destroy();
    header("Location: login.html");
    exit();
} else {
    echo "Error deleting account: " . $conn->error;
}

$conn->close();
?>
