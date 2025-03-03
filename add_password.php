<?php
session_start();
include_once("config.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!defined('ENCRYPTION_KEY')) {
    die("ENCRYPTION_KEY is not defined! Check config.php");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site_name = trim($_POST['site_name']);
    $site_username = trim($_POST['site_username']);
    $site_password = trim($_POST['site_password']);
    $user_id = $_SESSION['user_id'];

    if (empty($site_name) || empty($site_username) || empty($site_password)) {
        $error = "All fields are required!";
    } else {
        // Encrypt password
        $key = hex2bin(ENCRYPTION_KEY);
        $iv = openssl_random_pseudo_bytes(16);
        $encryptedPassword = openssl_encrypt($site_password, 'aes-256-cbc', $key, 0, $iv);
        
        $stmt = $conn->prepare("INSERT INTO passwords (user_id, site_name, username, password, encryption_key) VALUES (:user_id, :site_name, :site_username, :password, :iv)");
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":site_name", $site_name);
        $stmt->bindParam(":site_username", $site_username);
        $stmt->bindParam(":password", base64_encode($encryptedPassword));
        $stmt->bindParam(":iv", base64_encode($iv));
        
        if ($stmt->execute()) {
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Failed to save password!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Password - Password Monkey</title>
    <link rel="stylesheet" href="assets/bootstrap-5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">Password Monkey 🐒</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">🏠 Home</a></li>
                <li class="nav-item"><a class="nav-link" href="export.php">📂 Export</a></li>
                <li class="nav-item"><a class="btn btn-danger btn-sm ms-2" href="logout.php">🚪 Logout</a></li>
            </ul>
        </div>
    </div>
</nav>
<div class="container mt-5">
    <h2>Add New Password</h2>
    
    <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
    
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Website Name</label>
            <input type="text" name="site_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="site_username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="text" name="site_password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Save Password</button>
        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
