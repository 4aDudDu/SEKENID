<?php
session_start();
require 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $no_hp = $_POST['no_hp'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $_SESSION['register_error'] = "Passwords do not match!";
        $_SESSION['register_error_type'] = "danger";
    } else {
        $query = "SELECT * FROM akun WHERE username = '$username' OR email = '$email' OR no_hp = '$no_hp'";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $_SESSION['register_error'] = "Username, Email, or Phone Number already exists!";
            $_SESSION['register_error_type'] = "danger";
        } else {
            $query = "INSERT INTO akun (username, email, no_hp, password) VALUES ('$username', '$email', '$no_hp', '$password')";

            if ($conn->query($query) === TRUE) {
                $_SESSION['register_success'] = "Registration successful! Please login.";
                header("Location: login.php");
                exit;
            } else {
                $_SESSION['register_error'] = "Error: " . $query . "<br>" . $conn->error;
                $_SESSION['register_error_type'] = "danger";
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
    <title>Register - SEKENID</title>
    <link rel="stylesheet" href="style2.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
</head>

<body>
    <div class="container" id="target">
        <div class="login-container">
            <h1 class="judul">SEKENID</h1>
            <!-- Form register -->
            <form action="" method="post">
                <!-- Input username, email, phone number, password, and confirm password -->
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
                <!-- Register button -->
                <input type="submit" class="btn btn-outline-primary" value="Register">
            </form>

            <!-- Display alert message if there's any error -->
            <?php if (isset($_SESSION['register_error'])): ?>
                <div class="alert alert-<?php echo $_SESSION['register_error_type']; ?>" role="alert">
                    <?php echo $_SESSION['register_error']; ?>
                </div>
                <?php unset($_SESSION['register_error']); ?>
                <?php unset($_SESSION['register_error_type']); ?>
            <?php endif; ?>

            <!-- Display success message if registration is successful -->
            <?php if (isset($_SESSION['register_success'])): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $_SESSION['register_success']; ?>
                </div>
                <?php unset($_SESSION['register_success']); ?>
            <?php endif; ?>

            <!-- Link to login page -->
            <div class="card-footer text-center">
                <p>Sudah punya akun? <a href="login.php">Login disini</a></p>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
            $(".container").addClass("animate__animated animate__backInDown");
        });
    </script>
</body>

</html>