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

// First, delete dependent records in the saved_recipes table
$stmt = $conn->prepare("DELETE FROM saved_recipes WHERE user_id IN (SELECT id FROM users WHERE username=?)");
$stmt->bind_param("s", $username);
if (!$stmt->execute()) {
    echo "Error clearing user related data: " . $stmt->error;
    $stmt->close();
    $conn->close();
    exit();
}
$stmt->close();

// Now, delete the user
$stmt = $conn->prepare("DELETE FROM users WHERE username=?");
$stmt->bind_param("s", $username);

if ($stmt->execute() === TRUE) {
    echo "Account deleted successfully.";

    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
} else {
    echo "Error deleting account: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
