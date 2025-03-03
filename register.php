<?php
require 'config.php';

$message = ""; // Store feedback message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim(htmlspecialchars($_POST['fullname']));
    $username = trim(htmlspecialchars($_POST['username']));
    $password = trim($_POST['password']);

    if (empty($fullname) || empty($username) || empty($password)) {
        $message = '<div class="alert alert-danger">Please fill in all fields.</div>';
    } else {
        // Check if username exists
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = :username");
        $stmt->bindParam(":username", $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $message = '<div class="alert alert-warning">Username already taken. Choose another.</div>';
        } else {
            // Hash password securely
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            
            // Generate a secure recovery key
            $recoveryKey = bin2hex(random_bytes(16));

            try {
                $stmt = $conn->prepare("INSERT INTO users (fullname, username, password, recovery_key) VALUES (:fullname, :username, :password, :recovery_key)");
                $stmt->bindParam(":fullname", $fullname);
                $stmt->bindParam(":username", $username);
                $stmt->bindParam(":password", $hashedPassword);
                $stmt->bindParam(":recovery_key", $recoveryKey);

                if ($stmt->execute()) {
                    $message = '<div class="alert alert-success">Registration successful! <a href="login.php">Login here</a>.</div>';
                } else {
                    $message = '<div class="alert alert-danger">Error: Could not register user.</div>';
                }
            } catch (PDOException $e) {
                $message = '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Monkey 🐵 - Signup</title>
    <link rel="stylesheet" href="assets/bootstrap-5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/boxicons/css/boxicons.min.css">
    <style>
        .form-control { max-width: 400px; }
        .container { max-width: 500px; }
    </style>
    <style>
        .form-control { max-width: 400px; }
        .container { max-width: 500px; }
        .toggle-password { cursor: pointer; position: absolute; right: 10px; top: 50%; transform: translateY(-50%); }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Password Monkey 🐵</h2>

        <?php echo $message; ?>

        <form method="POST" action="" class="card p-4 mt-3">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="fullname" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" name="password" id="password" class="form-control" required>
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()">
                        <i class="bx bx-show" id="eyeIcon"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100">Signup</button>
        </form>

        <a href="login.php" class="btn btn-success my-3 w-100">Login</a>
        <a href="recover.php" class="btn-link d-block text-center">Forgot Password?</a>
    </div>

    <script>
        function togglePassword() {
            let passwordField = document.getElementById("password");
            let eyeIcon = document.getElementById("eyeIcon");
            if (passwordField.type === "password") {
                passwordField.type = "text";
                eyeIcon.classList.replace("bx-show", "bx-hide");
            } else {
                passwordField.type = "password";
                eyeIcon.classList.replace("bx-hide", "bx-show");
            }
        }
    </script>
</body>
</html>
