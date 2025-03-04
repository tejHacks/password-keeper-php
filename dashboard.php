<?php
session_start();
include_once("config.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Generate CSRF token if it does not exist
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id, site_name, username, password, encryption_key FROM passwords WHERE user_id = :user_id");
$stmt->bindParam(":user_id", $user_id);
$stmt->execute();
$passwords = $stmt->fetchAll(PDO::FETCH_ASSOC);

function decryptPassword($encryptedPassword, $encryptionKey) {
    $key = hex2bin(ENCRYPTION_KEY);
    $iv = base64_decode($encryptionKey);

    if (!$iv || strlen($iv) !== 16) {
        return "Decryption Error";
    }

    $decrypted = openssl_decrypt(base64_decode($encryptedPassword), 'aes-256-cbc', $key, 0, $iv);
    
    return $decrypted ?: "Decryption Error";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Password Monkey</title>
    <link rel="stylesheet" href="assets/bootstrap-5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/boxicons/css/boxicons.min.css">
    <style>
        body { background-color: #f8f9fa; color: #212529; }
        .container { max-width: 800px; margin-top: 50px; }
        .card { padding: 20px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); border-radius: 10px; }
        .copy-btn, .toggle-password { cursor: pointer; color: #007bff; margin-left: 8px; }
        .copy-btn:hover, .toggle-password:hover { color: #0056b3; }
    </style>
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

<div class="container">
    <div class="d-flex justify-content-between align-items-center mt-3">
        <a href="add_password.php" class="btn btn-primary">+ Add New Password</a>
    </div>
    
    <div class="card mt-3">
        <h4 class="mb-3">Your Saved Passwords</h4>
        
        <?php if (empty($passwords)): ?>
            <div class="text-center p-5">
                <i class="bx bx-key" style="font-size: 40px;"></i>
                <h5 class="mt-3">No passwords saved yet</h5>
                <p>Click below to add a new password.</p>
                <a href="add_password.php" class="btn btn-primary">+ Add New Password</a>
            </div>
        <?php else: ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Website</th>
                        <th>Password</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($passwords as $row): ?>
                    <tr>
                        <td> <?= htmlspecialchars($row['site_name']) ?></td>
                        <td>
                            <span class="password-container">
                                <span class="password-hidden" id="pass-<?= $row['id'] ?>">********</span>
                                <span class="password-visible d-none" id="pass-text-<?= $row['id'] ?>">
                                    <?= htmlspecialchars(decryptPassword($row['password'], $row['encryption_key'])) ?>
                                </span>
                                <i class="bx bx-show toggle-password" data-id="<?= $row['id'] ?>"></i>
                                <i class="bx bx-copy copy-btn" onclick="copyToClipboard('pass-text-<?= $row['id'] ?>')"></i>
                            </span>
                        </td>
                        <td>
                            <form action="edit.php" method="POST" class="d-inline">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                <button type="submit" class="btn btn-sm btn-warning">Edit</button>
                            </form>
                            
                            <form action="delete.php" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <a href="export.php" class="btn btn-success">Export to CSV</a>
        <?php endif; ?>
    </div>
</div>

<script src="assets/bootstrap-5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.toggle-password').forEach(item => {
    item.addEventListener('click', function() {
        let id = this.getAttribute('data-id');
        let hiddenPass = document.getElementById('pass-' + id);
        let visiblePass = document.getElementById('pass-text-' + id);

        if (visiblePass.classList.contains('d-none')) {
            hiddenPass.classList.add('d-none');
            visiblePass.classList.remove('d-none');
        } else {
            hiddenPass.classList.remove('d-none');
            visiblePass.classList.add('d-none');
        }
    });
});

function copyToClipboard(id) {
    let textElement = document.getElementById(id);
    if (textElement.classList.contains('d-none')) {
        alert('Password is hidden. Click the eye icon to reveal it first.');
        return;
    }

    let text = textElement.innerText;
    navigator.clipboard.writeText(text).then(() => alert('Password copied!'));
}
</script>
</body>
</html>
