<?php
define('ENCRYPTION_KEY', 'c9cd3de8bc96df33a61151290b119dbe688efe1c1d5bb78d346d9d67b443aa8');

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
