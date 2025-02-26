<?php
require 'config.php';

$message = ""; // Store feedback message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $message = '<div class="alert alert-danger">Please fill in all fields.</div>';
    } else {
        // Hash password before storing
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        try {
            $stmt = $conn->prepare("INSERT INTO `users` (`username`, `password_hash`) VALUES (:username, :password)");
            $stmt->bindParam(":username", $username);
            $stmt->bindParam(":password", $hashedPassword);

            if ($stmt->execute()) {
                $message = '<div class="alert alert-success">Registration successful!</div>';
            } else {
                $message = '<div class="alert alert-danger">Error: Could not register user.</div>';
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
    <title>Register - Password Monkey</title>
    <link rel="stylesheet" href="assets/bootstrap-5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/boxicons/css/boxicons.min.css">
    
</head>
<body class="dark-mode">
    <div class="container mt-5">
        <h2 class="text-center">Register on Password Monkey</h2>

        <!-- Alert Message -->
        <?php echo $message; ?>

        <form method="POST" action="">
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
            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>
        <a class="btn btn-success my-3 w-100" href="login.php">Login</a>
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
