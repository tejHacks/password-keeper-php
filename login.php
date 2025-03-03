<?php
require 'config.php';
session_start();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $message = '<div class="alert alert-danger">Please fill in all fields.</div>';
    } else {
        try {
            $stmt = $conn->prepare("SELECT user_id, username, password FROM users WHERE username = :username");
            $stmt->bindParam(":username", $username);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                header("Location: dashboard.php");
                exit;
            } else {
                $message = '<div class="alert alert-danger">Invalid username or password.</div>';
            }
        } catch (PDOException $e) {
            $message = '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Monkey 🐵 - Login</title>
    <link rel="stylesheet" href="assets/bootstrap-5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/boxicons/css/boxicons.min.css">
    <style>
        .form-control { max-width: 400px; }
        .container { max-width: 500px; }
        .toggle-password { cursor: pointer; position: absolute; right: 10px; top: 50%; transform: translateY(-50%); }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Password Monkey 🐵</h2>
        <div class="card p-4 mt-3">
            <h3 class="text-center">Login</h3>
            <?php echo $message; ?>
            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3 position-relative">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <input type="password" id="password" name="password" class="form-control" required>
                        <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()">
                            <i class="bx bx-show" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
            <a href="register.php" class="btn btn-success w-100 my-2">Create Account</a>
            <a href="recover.php" class="btn-link d-block text-center">Forgot Password?</a>
        </div>
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
