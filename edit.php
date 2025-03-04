<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (headers_sent()) {
    die("Headers already sent. Check for whitespace or output before header()");
}

include_once("config.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!defined('ENCRYPTION_KEY')) {
    die("ENCRYPTION_KEY is not defined! Check config.php");
}

$user_id = $_SESSION['user_id'];

// Ensure CSRF token is set in session
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Check if editing an existing password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['csrf_token'])) {
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }

    $id = $_POST['id'];
    $stmt = $conn->prepare("SELECT site_name, username, password, encryption_key FROM passwords WHERE id = :id AND user_id = :user_id");
    $stmt->bindParam(":id", $id);
    $stmt->bindParam(":user_id", $user_id);
    $stmt->execute();
    $passwordData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$passwordData) {
        die("Password not found");
    }

    function decryptPassword($encryptedPassword, $encryptionKey) {
        $key = hex2bin(ENCRYPTION_KEY);
        $iv = base64_decode($encryptionKey);
        return openssl_decrypt(base64_decode($encryptedPassword), 'aes-256-cbc', $key, 0, $iv);
    }

    $decryptedPassword = decryptPassword($passwordData['password'], $passwordData['encryption_key']);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }

    $id = $_POST['id'];
    $site_name = $_POST['site_name'];
    $username = $_POST['username'];
    $new_password = $_POST['password'];

    function encryptPassword($password, &$encryptionKey) {
        $key = hex2bin(ENCRYPTION_KEY);
        $iv = random_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encryptionKey = base64_encode($iv);
        return base64_encode(openssl_encrypt($password, 'aes-256-cbc', $key, 0, $iv));
    }

    $encryptedPassword = encryptPassword($new_password, $encryptionKey);
    
    $stmt = $conn->prepare("UPDATE passwords SET site_name = :site_name, username = :username, password = :password, encryption_key = :encryption_key WHERE id = :id AND user_id = :user_id");
    $stmt->bindParam(":site_name", $site_name);
    $stmt->bindParam(":username", $username);
    $stmt->bindParam(":password", $encryptedPassword);
    $stmt->bindParam(":encryption_key", $encryptionKey);
    $stmt->bindParam(":id", $id);
    $stmt->bindParam(":user_id", $user_id);
    if ($stmt->execute()) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        header("Location: dashboard.php");
        // Regenerate CSRF token after successful update
        exit;
    } else {
        var_dump($stmt->errorInfo()); // Debug SQL errors
        exit;
    }
} else {
    die("Invalid request");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Password - Password Monkey</title>
    <link rel="stylesheet" href="assets/bootstrap-5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Edit Password</h2>
    <form action="edit.php" method="POST">
        <input type="hidden" name="id" value="<?= htmlspecialchars($id ?? '') ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <div class="mb-3">
            <label class="form-label">Website Name</label>
            <input type="text" name="site_name" class="form-control" value="<?= htmlspecialchars($passwordData['site_name'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($passwordData['username'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="text" name="password" class="form-control" value="<?= htmlspecialchars($decryptedPassword ?? '') ?>" required>
        </div>
        <button type="submit" name="update" class="btn btn-primary">Update</button>
        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
