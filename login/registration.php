<?php

//no need for this 3 lines, just there for debugging. this reports error if found any
// all the error echo statements throughout the code are also there for debugging. can be just removed if wanted
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = "localhost";
$user = "rpatel245";
$pass = "rpatel245";
$dbname = "rpatel245";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) 
{
    die("Connection failed: " . $conn->connect_error);
}
  
  
$sql_create_db = "CREATE DATABASE IF NOT EXISTS rpatel245";
if ($conn->query($sql_create_db) === TRUE) 
{
   
    $conn->close();
    $conn = new mysqli($host, $user, $pass, "rpatel245");
  
    $sql_create_table = "CREATE TABLE IF NOT EXISTS users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(30) NOT NULL,
    last_name VARCHAR(30) NOT NULL,  
    email VARCHAR(50) NOT NULL,
    username VARCHAR(30) NOT NULL,
    password VARCHAR(255) NOT NULL,
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
  
    if ($conn->query($sql_create_table) === TRUE) 
    {
  
        if ($_SERVER["REQUEST_METHOD"] == "POST") 
        {
            $firstName = $_POST['firstName'];
            $lastName = $_POST['lastName'];
            $email = $_POST['email'];
            $username = $_POST['username'];
            $password = $_POST['password'];
  
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
  
            $sql_insert_user = "INSERT INTO users (first_name, last_name, email, username, password) 
            VALUES ('$firstName', '$lastName', '$email', '$username', '$hashedPassword')";
  
            if ($conn->query($sql_insert_user) === TRUE) 
            {
                echo "Registration successful!";
				echo "<a href=login.html> Login Here!</a> ";
            } 
            else 
            {
                echo "Error: " . $sql_insert_user . "<br>" . $conn->error;
            }
        }
    } 
    
    else 
    {
        echo "Error creating table: " . $conn->error;
    }
} 

else
{
    echo "Error creating database: " . $conn->error;
}
  
$conn->close();

