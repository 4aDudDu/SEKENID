<?php
session_start();
require 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $no_hp = $_POST['no_hp'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $confirm_password = $_POST['confirm_password'];

    if ($_POST['password'] !== $confirm_password) {
        $_SESSION['register_error'] = "Passwords do not match!";
        $_SESSION['register_error_type'] = "danger";
    } else {
        $query = "SELECT * FROM akun WHERE username = '$username' OR email = '$email' OR no_hp = '$no_hp'";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $_SESSION['register_error'] = "Username, Email, or Phone Number already exists!";
            $_SESSION['register_error_type'] = "danger";
        } else {
            $insert_query = "INSERT INTO akun (username, email, no_hp, password) VALUES ('$username', '$email', '$no_hp', '$password')";
            if ($conn->query($insert_query) === TRUE) {
                $_SESSION['register_success'] = "Registration successful! Please log in.";
                $_SESSION['register_success_type'] = "success";
                header("Location: login.php");
                exit;
            } else {
                $_SESSION['register_error'] = "Error: " . $conn->error;
                $_SESSION['register_error_type'] = "danger";
            }
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - SEKENID</title>
    <link rel="stylesheet" href="login.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
</head>

<body>
    <div class="container" id="target">
        <div class="login-container">
            <h1 class="h1">DAFTAR</h1>
            <form id="register-form" action="" method="post">
                <div class="mb-3">
                    <label for="username" class="form-label">Username:</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="no_hp" class="form-label">No HP:</label>
                    <input type="text" class="form-control" id="no_hp" name="no_hp" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password:</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" class="oauthButton">Register</button>
            </form>
            <div class="separator">
                <div></div>
                <span>OR</span>
                <div></div>
            </div>
            <button class="oauthButton" onclick="window.location.href='login.php'">Login</button>

            <?php if (isset($_SESSION['register_error'])): ?>
                <div class="alert alert-<?php echo $_SESSION['register_error_type']; ?> mt-3 animate__animated animate__fadeInDown"
                    id="error-message">
                    <?php echo $_SESSION['register_error'];
                    unset($_SESSION['register_error']);
                    unset($_SESSION['register_error_type']); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const errorMessage = document.getElementById('error-message');
            if (errorMessage) {
                setTimeout(() => {
                    errorMessage.classList.remove('animate__fadeInDown');
                    errorMessage.classList.add('animate__fadeOutUp');
                }, 3000);
            }
        });
    </script>
</body>

</html>