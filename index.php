<?php
include 'koneksi.php';
session_start();
$is_logged_in = isset($_SESSION['username']); // Periksa apakah pengguna sudah login

// Handle logout
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit();
}

$selected_category = isset($_POST['category']) ? $_POST['category'] : '';

// Adjust SQL query based on selected category
if ($selected_category) {
    $stmt = $conn->prepare("SELECT * FROM produk WHERE kategori = ?");
    $stmt->bind_param("s", $selected_category);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT * FROM produk";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEKENID</title>
    <link rel="stylesheet" href="style.css">
    <link rel="website icon" type="png" href="assets/sekenid.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.css" rel="stylesheet" />
</head>

<body class="flex flex-col h-screen justify-between">
    <nav
        class="navbar-bg bg-transparent dark:bg-gray-900 fixed w-full z-20 top-0 start-0 border-b border-gray-200 dark:border-gray-600">
        <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
            <a href="index.php" class="flex items-center space-x-3 rtl:space-x-reverse">
                <img src="assets/sekenid.png" class="h-20" alt="SEKENID Logo">
                <span class="seken-text self-center text-2xl whitespace-nowrap dark:text-white">SEKENID</span>
            </a>
            <div class="flex md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse">
                <img src="assets/cart.png" alt="" class="h-10 cart-ico" />
                <?php if ($is_logged_in): ?>
                    <form method="POST" action="index.php" class="inline">
                        <button type="submit" name="logout"
                            class="text-white bg-red-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Keluar</button>
                    </form>
                <?php else: ?>
                    <button type="button" id="show-wrapper-button"
                        class="text-white bg-red-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Masuk</button>
                <?php endif; ?>
                <button data-collapse-toggle="navbar-sticky" type="button"
                    class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600"
                    aria-controls="navbar-sticky" aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 17 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M1 1h15M1 7h15M1 13h15" />
                    </svg>
                </button>
            </div>
            <div class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1" id="navbar-sticky">
                <ul
                    class="navbar-drop flex flex-col p-4 md:p-0 mt-4 font-medium border rounded-lg bg-gray-50 md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-transparent dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700">
                    <li>
                        <a href="index.php"
                            class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-700 md:p-0 md:dark:hover:text-blue-500 dark:text-white dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700">Dashboard</a>
                    </li>
                    <li>
                        <form method="POST" action="index.php">
                            <div class="dropdown">
                                <a class="dropdown-toggle block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-700 md:p-0 md:dark:hover:text-blue-500 dark:text-white dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700"
                                    role="button" data-bs-toggle="dropdown" aria-expanded="false">Kategori</a>
                                <ul class="dropdown-menu">
                                    <li><button class="dropdown-item" type="submit" name="category"
                                            value="Kendaraan">Kendaraan</button></li>
                                    <li><button class="dropdown-item" type="submit" name="category"
                                            value="Pakaian">Pakaian</button></li>
                                    <li><button class="dropdown-item" type="submit" name="category"
                                            value="Elektronik">Elektronik</button></li>
                                    <li><button class="dropdown-item" type="submit" name="category"
                                            value="Tanah dan Bangunan">Tanah dan Bangunan</button></li>
                                    <li><button class="dropdown-item" type="submit" name="category"
                                            value="Mainan Anak - Anak">Mainan Anak - Anak</button></li>
                                    <li><button class="dropdown-item" type="submit" name="category"
                                            value="Perabotan">Perabotan</button></li>
                                </ul>
                            </div>
                        </form>
                    </li>
                    <li>
                        <a href="laporan.php"
                            class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-700 md:p-0 md:dark:hover:text-blue-500 dark:text-white dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700">Laporan</a>
                    </li>
                    <li>
                        <a href="#"
                            class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-700 md:p-0 md:dark:hover:text-blue-500 dark:text-white dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700">Tentang
                            Kami</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- container -->
    <div class="container">
        <div class="cards-container">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='card'>";
                    echo "<div class='card-img'>";
                    echo "<img src='" . $row['foto'] . "' alt='Product Image' />";
                    echo "</div>";
                    echo "<div class='card-info'>";
                    echo "<p class='text-title'>" . $row['namabarang'] . "</p>";
                    echo "<p class='text-body'>Kategori: " . $row['kategori'] . "<br>Jenis: " . $row['jenis'] . "</p>";
                    echo "</div>";
                    echo "<div class='card-footer'>";
                    echo "<span class='text-title'>Rp. " . number_format($row['harga'], 0, ',', '.') . "</span>";
                    echo "<button class='btn-beli'>Beli</button>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "0 results";
            }
            ?>
        </div>

        <!-- jual tombol -->
        <?php if ($is_logged_in): ?>
            <button class="btnsell" onclick="window.location.href='jual.php'">
                <img src="assets/jualbrg.png" alt="" />
            </button>
        <?php endif; ?>
    </div>

    <footer class="bg-white-800 text-black text-center py-4">
        <p>&copy; 2024 SEKENID. All rights reserved.</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('show-wrapper-button').addEventListener('click', function () {
                window.location.href = 'login.php';
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>