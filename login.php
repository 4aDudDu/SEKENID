<?php
session_start();
require 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (isset($_POST['guest'])) {
        header("Location: home.php?guest=true");
        exit;
    } else {
        $query = "SELECT * FROM akun WHERE username = '$username'";
        $result = $conn->query($query);

        if ($result === false) {
            die("Query Error: " . $conn->error);
        }

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['loggedin'] = true;
                $_SESSION['username'] = $username;
                $_SESSION['id'] = $user['id'];
                header("Location: index.php?message=welcome");
                exit;
            } else {
                $_SESSION['login_error'] = "Password salah!";
                $_SESSION['login_error_type'] = "danger";
                header("Location: login.php");
                exit;
            }
        } else {
            $_SESSION['login_error'] = "Akun Tidak Terdaftar!";
            $_SESSION['login_error_type'] = "danger";
            header("Location: login.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SEKENID</title>
    <link rel="stylesheet" href="style2.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
</head>

<body>
    <div class="container" id="target">
        <div class="login-container">
            <h1 class="judul">SEKENID</h1>
            <form action="" method="post">
                <div class="mb-3">
                    <label for="username" class="form-label">Username:</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <input type="submit" class="btn btn-outline-primary" value="Login">
            </form>

            <?php if (isset($_SESSION['login_error'])): ?>
                <div class="alert alert-<?php echo $_SESSION['login_error_type']; ?> mt-3 animate__animated animate__fadeInDown"
                    id="error-message">
                    <?php echo $_SESSION['login_error'];
                    unset($_SESSION['login_error']);
                    unset($_SESSION['login_error_type']); ?>
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