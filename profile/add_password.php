<?php
session_start();
require '../config.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$message = ""; // Store success/error messages

// Encryption function with IV storage
function encryptPassword($password) {
    $key = hex2bin(ENCRYPTION_KEY);
    $iv = random_bytes(openssl_cipher_iv_length('aes-256-cbc')); // Generate random IV
    $encrypted = openssl_encrypt($password, 'aes-256-cbc', $key, 0, $iv);
    
    return [
        'encrypted_password' => base64_encode($encrypted),
        'iv' => base64_encode($iv) // Store IV separately
    ];
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $website = trim($_POST['website']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $user_id = $_SESSION['user_id']; // Get user_id from session

    if (empty($website) || empty($username) || empty($password)) {
        $message = '<div class="alert alert-danger">All fields are required!</div>';
    } else {
        try {
            $encryptionResult = encryptPassword($password); 
            $encryptedPassword = $encryptionResult['encrypted_password'];
            $iv = $encryptionResult['iv']; // Store IV separately

            $stmt = $conn->prepare("INSERT INTO passwords (user_id, website, username, encrypted_password, iv) VALUES (:user_id, :website, :username, :password, :iv)");
            $stmt->bindParam(":user_id", $user_id);
            $stmt->bindParam(":website", $website);
            $stmt->bindParam(":username", $username);
            $stmt->bindParam(":password", $encryptedPassword);
            $stmt->bindParam(":iv", $iv);

            if ($stmt->execute()) {
                $message = '<div class="alert alert-success">Password saved securely! 🔒</div>';
            } else {
                $message = '<div class="alert alert-danger">Error saving password.</div>';
            }
        } catch (PDOException $e) {
            $message = '<div class="alert alert-danger">Database error: ' . $e->getMessage() . '</div>';
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
    <link rel="stylesheet" href="../assets/bootstrap-5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/boxicons/css/boxicons.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            transition: 0.3s;
        }
        .container {
            max-width: 400px;
            margin-top: 50px;
        }
        .card {
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        .dark-mode {
            background-color: #121212;
            color: white;
        }
        .dark-mode .card {
            background-color: #1e1e1e;
            color: white;
        }
        .dark-mode .form-control {
            background-color: #2a2a2a;
            color: white;
        }
        .dark-mode .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .password-container {
            position: relative;
        }
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 70%;
            transform: translateY(-50%);
            cursor: pointer;
            color: black;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Add Password</h2>
            <button class="btn btn-outline-secondary" id="toggleDarkMode">🌙 Dark Mode</button>
        </div>
        <div class="card mt-3">
            <h4 class="mb-3">Save a New Password</h4>
            <?= $message ?>
            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label">Website</label>
                    <input type="text" name="website" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3 password-container">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                    <i class="bx bx-show toggle-password" id="togglePassword"></i>
                </div>
                <button type="submit" class="btn btn-primary w-100">Save Password</button>
            </form>
            <div class="text-center mt-3">
                <a href="dashboard.php" class="btn btn-outline-secondary">Back to Dashboard</a>
            </div>
        </div>
    </div>

    <script>
        // Dark mode toggle
        document.getElementById("toggleDarkMode").addEventListener("click", function() {
            document.body.classList.toggle("dark-mode");
            localStorage.setItem("darkMode", document.body.classList.contains("dark-mode"));
        });

        // Load dark mode preference
        if (localStorage.getItem("darkMode") === "true") {
            document.body.classList.add("dark-mode");
        }

        // Password visibility toggle
        document.getElementById("togglePassword").addEventListener("click", function() {
            let passwordField = document.getElementById("password");
            if (passwordField.type === "password") {
                passwordField.type = "text";
                this.classList.replace("bx-show", "bx-hide");
            } else {
                passwordField.type = "password";
                this.classList.replace("bx-hide", "bx-show");
            }
        });
    </script>
</body>
</html>
