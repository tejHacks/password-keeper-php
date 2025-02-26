<?php
$host = "localhost"; // Change if needed
$user = "root"; // Your MySQL username
$pass = ""; // Your MySQL password
$dbname = "password_keeper"; // Database name

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connection successful";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
