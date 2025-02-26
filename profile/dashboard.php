<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require '../config.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['user'];

// Fetch user's saved passwords
try {
    $stmt = $conn->prepare("SELECT * FROM passwords WHERE `username` = :user");
    $stmt->bindParam(":user", $username);
    $stmt->execute();
    $passwords = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        .dark-mode .btn {
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
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Password Manager</h2>
            <button class="btn btn-outline-secondary" id="toggleDarkMode">🌙 Dark Mode</button>
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
                                <span id="pass-<?= $pass['id'] ?>"><?= htmlspecialchars($pass['password']) ?></span>
                                <i class="bx bx-copy copy-btn" onclick="copyToClipboard('pass-<?= $pass['id'] ?>')"></i>
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
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>

    <script>
        function copyToClipboard(id) {
            var text = document.getElementById(id).innerText;
            navigator.clipboard.writeText(text).then(() => {
                alert("Copied to clipboard!");
            }).catch(err => {
                console.error('Error copying text: ', err);
            });
        }

        // Dark mode toggle
        document.getElementById("toggleDarkMode").addEventListener("click", function() {
            document.body.classList.toggle("dark-mode");
        });
    </script>
</body>
</html>
