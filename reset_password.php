<?php
require 'config.php';

session_start();

if (!isset($_SESSION['verified_user'])) {
    header("Location: recover.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($new_password) || empty($confirm_password)) {
        $message = '<div class="alert alert-danger">Please fill in all fields.</div>';
    } elseif ($new_password !== $confirm_password) {
        $message = '<div class="alert alert-warning">Passwords do not match.</div>';
    } else {
        $hashedPassword = password_hash($new_password, PASSWORD_BCRYPT);

        try {
            $stmt = $conn->prepare("UPDATE users SET password = :password WHERE username = :username");
            $stmt->bindParam(":password", $hashedPassword);
            $stmt->bindParam(":username", $_SESSION['verified_user']);

            if ($stmt->execute()) {
                session_destroy();
                header("Location: login.php?reset_success=1");
                exit;
            } else {
                $message = '<div class="alert alert-danger">Error: Could not update password.</div>';
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
    <title>Reset Password - Password Monkey</title>
    <link rel="stylesheet" href="assets/bootstrap-5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">🐵 Password Monkey</h2>
        <div class="card p-4 mx-auto" style="max-width: 400px;">
            <h4 class="text-center">Reset Password</h4>
            <?php echo $message; ?>
            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <input type="password" name="new_password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Reset Password</button>
            </form>
        </div>
    </div>
</body>
</html>
