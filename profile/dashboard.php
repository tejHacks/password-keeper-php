<?php
session_start();
require '../config.php';
if (!defined('ENCRYPTION_KEY')) {
    die("ENCRYPTION_KEY is not defined! Check config.php");
}


if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];



// Fetch user's saved passwords with IV
try {
    $stmtUser = $conn->prepare("SELECT id, username, email FROM users WHERE id = :user_id LIMIT 1");
    $stmtUser->bindParam(":user_id", $user_id);
    $stmtUser->execute();
    $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("User not found!");
    }

    // Ensure ENCRYPTION_KEY is defined
if (!defined('ENCRYPTION_KEY')) {
    die("Encryption key is missing!");
}

function decryptPassword($encryptedPassword, $iv) {
    if (!$encryptedPassword || !$iv) {
        return '[Decryption Failed]';
    }

    $key = hex2bin(ENCRYPTION_KEY);
    if (!$key) {
        die("Encryption key conversion failed!");
    }
    
  
    $decodedEncrypted = base64_decode($encryptedPassword);
    $decodedIV = base64_decode($iv);

    return openssl_decrypt($decodedEncrypted, 'aes-256-cbc', $key, 0, $decodedIV) ?: '[Decryption Failed]';
}
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Password Monkey</title>
    <link rel="stylesheet" href="../assets/bootstrap-5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/boxicons/css/boxicons.min.css">
  
    <style>
        body {
            background-color: #f8f9fa;
            color: #212529;
        }
        .container {
            max-width: 800px;
            margin-top: 50px;
        }
        .card {
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        .copy-btn {
            cursor: pointer;
            color: #007bff;
        }
        .copy-btn:hover {
            color: #0056b3;
        }
        .dark-mode {
            background-color: #121212;
            color: white;
        }
        .dark-mode .card {
            background-color: #1e1e1e;
            color: white;
        }
        .dark-mode #darkBtn {
            background-color: #007bff;
            color: white;
        }
        .empty-state {
            text-align: center;
            padding: 50px;
        }
        .empty-state img {
            max-width: 200px;
            opacity: 0.8;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">Password Monkey 🐒</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">🏠 Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="export.php">📂 Export</a>
                </li>
                <li class="nav-item">
                    <button id="darkBtn" class="btn btn-sm btn-outline-light">🌙 Dark Mode</button>
                </li>
                <li class="nav-item">
                    <a class="btn btn-danger btn-sm ms-2" href="logout.php">🚪 Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mt-3">
        <h2>Hey, <?= htmlspecialchars($username) ?></h2>
        <a href="add_password.php" class="btn btn-primary">+ Add New Password</a>
    </div>

    <div class="card mt-3">
        <h4 class="mb-3">Your Saved Passwords</h4>

        <?php if (empty($passwords)): ?>
            <div class="empty-state">
                <img src="../assets/images/empty.gif" alt="No data">
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
                    <?php foreach ($passwords as $pass): ?>
                    <tr>
                        <td><?= htmlspecialchars($pass['website']) ?></td>
                        <td>
                            <span class="password-container">
                                <span class="password-hidden" id="pass-<?= $pass['id'] ?>">********</span>
                                <span class="password-visible d-none" id="pass-text-<?= $pass['id'] ?>">
                                    <?= htmlspecialchars(decryptPassword($pass['password'], $pass['iv'])) ?>
                                </span>
                                <i class="bx bx-show toggle-password" data-id="<?= $pass['id'] ?>"></i>
                                <i class="bx bx-copy copy-btn" onclick="copyToClipboard('pass-text-<?= $pass['id'] ?>')"></i>
                            </span>
                        </td>
                        <td>
                            <a href="edit.php?id=<?= $pass['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="delete.php?id=<?= $pass['id'] ?>" class="btn btn-sm btn-danger">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <a href="export.php" class="btn btn-success">Export to CSV</a>
        <?php endif; ?>
    </div>
</div>

<script>
    // Dark mode toggle with persistence
    document.addEventListener("DOMContentLoaded", function() {
        if (localStorage.getItem("darkMode") === "enabled") {
            document.body.classList.add("dark-mode");
        }

        document.getElementById("darkBtn").addEventListener("click", function() {
            document.body.classList.toggle("dark-mode");
            localStorage.setItem("darkMode", document.body.classList.contains("dark-mode") ? "enabled" : "disabled");
        });
    });

    // Toggle password visibility
    document.querySelectorAll(".toggle-password").forEach(btn => {
        btn.addEventListener("click", function () {
            let id = this.getAttribute("data-id");
            let hidden = document.getElementById(`pass-${id}`);
            let visible = document.getElementById(`pass-text-${id}`);

            hidden.classList.toggle("d-none");
            visible.classList.toggle("d-none");
            this.classList.toggle("bx-hide");
            this.classList.toggle("bx-show");
        });
    });

    // Copy to clipboard function
    function copyToClipboard(id) {
        var text = document.getElementById(id).innerText;
        navigator.clipboard.writeText(text).then(() => {
            alert("Copied to clipboard!");
        }).catch(err => {
            console.error('Error copying text: ', err);
        });
    }
</script>
<script src="../assets/bootstrap-5.3.3/dist/js/bootstrap.bundle.js"></script>
    <script src="../assets/bootstrap-5.3.3/dist/js/bootstrap.min.js"></script>
    <script src="../assets/bootstrap-5.3.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
