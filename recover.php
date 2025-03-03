<?php
require 'config.php';
session_start();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['verify'])) {
    $username = trim($_POST['username']);
    $recovery_key = trim($_POST['recovery_key']);

    if (empty($username) || empty($recovery_key)) {
        $message = '<div class="alert alert-danger">Please fill in all fields.</div>';
    } else {
        try {
            $stmt = $conn->prepare("SELECT * FROM `users` WHERE `username` = :username AND `recovery_key` = :recovery_key");
            $stmt->bindParam(":username", $username);
            $stmt->bindParam(":recovery_key", $recovery_key);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $_SESSION['reset_user'] = $user['user_id'];
                header("Location: reset_password.php");
                exit;
            } else {
                $message = '<div class="alert alert-danger">Invalid credentials.</div>';
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
    <title>Password Recovery - Password Monkey</title>
    <link rel="stylesheet" href="assets/bootstrap-5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="card p-4" style="width: 350px;">
            <h2 class="text-center text-dark">Recover Password</h2>
            <?php echo $message; ?>
            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label text-dark">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label text-dark">Recovery Key</label>
                    <input type="text" name="recovery_key" class="form-control" required>
                </div>
                <button type="submit" name="verify" class="btn btn-primary w-100">Verify</button>
            </form>
            <div class="text-center mt-3">
                <a href="login.php" class="text-decoration-none text-dark">Back to Login</a>
            </div>
        </div>
    </div>
</body>
</html>
