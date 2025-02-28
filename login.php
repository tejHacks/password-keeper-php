<?php
require 'config.php';
session_start();

$message = ""; // Store feedback message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $message = '<div class="alert alert-danger">Please fill in all fields.</div>';
    } else {
        try {
            $stmt = $conn->prepare("SELECT * FROM `users` WHERE `username` = :username");
            $stmt->bindParam(":username", $username);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password_hash'])) {
                // Store user ID and username in session
                $_SESSION['user_id'] = $user['id']; // Store user_id
                $_SESSION['username'] = $user['username']; // Store username

                header("Location: profile/dashboard.php"); // Redirect to dashboard
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
    <title>Login - Password Monkey</title>
    <link rel="stylesheet" href="assets/bootstrap-5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/boxicons/css/boxicons.min.css">
    <style>
       
        .card {
            background-color: #1e1e1e;
            border: none;
        }
        .form-control {
            background-color: #2a2a2a;
            color: white;
            border: 1px solid #444;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: none;
        }
        .toggle-password {
           
    cursor: pointer;
    position: absolute;
    right: 15px;
    top: 69%;
    transform: translateY(-43%);
    color: #000000;
        }
        .toggle-password:hover {
            color: grey;
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="card p-4" style="width: 350px;">
            <h2 class="text-center text-light">Login</h2>
            <?php echo $message; ?>
            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label text-light">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3 position-relative">
                    <label class="form-label text-light">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                    <i class="bx bx-show toggle-password" id="togglePassword"></i>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
            <div class="text-center mt-3">
                <a href="register.php" class="text-decoration-none text-light">Create an account</a>
            </div>
        </div>
    </div>

    <script>
        document.getElementById("togglePassword").addEventListener("click", function() {
            const passwordField = document.getElementById("password");
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
