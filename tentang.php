<?php
include 'koneksi.php';
session_start();
$is_logged_in = isset($_SESSION['username']);
$user = $_SESSION['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    if (!$is_logged_in) {
        header('Location: login.php');
        exit();
    }

    $product_id = $_POST['product_id'];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (!in_array($product_id, $_SESSION['cart'])) {
        $_SESSION['cart'][] = $product_id;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['chat'])) {
    if (!$is_logged_in) {
        header('Location: login.php');
        exit();
    }

    $product_id = $_POST['product_id'];
    $seller_id = $_POST['seller_id'];

    $stmt = $conn->prepare("SELECT room_id FROM chat_rooms WHERE buyer_id = ? AND seller_id = ?");
    $stmt->bind_param("ii", $user, $seller_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO chat_rooms (buyer_id, seller_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user, $seller_id);
        $stmt->execute();
        $room_id = $stmt->insert_id;
    } else {
        $row = $result->fetch_assoc();
        $room_id = $row['room_id'];
    }

    header("Location: chat.php?room_id=$room_id");
    exit();
}

$selected_category = isset($_POST['category']) ? $_POST['category'] : '';

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
    <title>Tentang</title>
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
                <a href="cart.php"><img src="assets/cart.png" alt="" class="h-10 cart-ico" /></a>
                <a href="list_chat.php"><img src="assets/chat.png" alt="" class="h-10 cart-ico" /></a>
                <?php if ($is_logged_in): ?>
                    <form method="POST" action="index.php" class="inline">
                        <button type="submit" name="logout"
                            class="text-white bg-red-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Keluar</button>
                    </form>
                <?php else: ?>
                    <a href="login.php"
                        class="text-white bg-red-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Masuk</a>
                <?php endif; ?>
                <button data-collapse-toggle="navbar-sticky" type="button"
                    class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600"
                    aria-controls="navbar-sticky" aria-expanded="false">
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
                        <a href="tentang.php"
                            class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-700 md:p-0 md:dark:hover:text-blue-500 dark:text-white dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700">Tentang
                            Kami</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1 style="font-size: xx-large; font-weight:bold; text-align: center;">SEKENID</h1>
        <br>
        <p style="text-align: justify;">
            SEKENID adalah platform jual beli barang berbasis website yang berfungsi sebagai marketplace penjualan,
            membantu mitra dalam menjual berbagai jenis barang. SEKENID menyediakan ruang bagi usaha mikro, kecil, dan
            menengah (UMKM) untuk memasarkan produk mereka, serta menawarkan berbagai barang baru dan barang bekas yang
            berkualitas. Dengan metode pembayaran yang fleksibel, termasuk opsi COD (Cash on Delivery) antara pembeli
            dan penjual, SEKENID memastikan proses transaksi yang aman dan nyaman. Platform ini tidak hanya memudahkan
            pembeli untuk menemukan barang yang mereka butuhkan, tetapi juga memberikan kesempatan kepada penjual untuk
            menjangkau pasar yang lebih luas, sehingga meningkatkan potensi penjualan mereka. Dengan antarmuka yang
            user-friendly dan fitur-fitur canggih, SEKENID berkomitmen untuk memberikan pengalaman belanja online yang
            optimal bagi semua penggunanya.
        </p>
        <br>
        <p style="text-align: justify;">
            SEKENID ini merupakan bagian dari P2MW, yaitu program pengembangan usaha mahasiswa yang telah memiliki usaha
            melalui bantuan dana pengembangan dan pembinaan dengan melakukan pendampingan serta pelatihan (coaching)
            usaha kepada mahasiswa peserta P2MW. Program ini dianggotai oleh mahasiswa dari Universitas Muhammadiyah
            Riau, bertujuan untuk memperkuat dan mengembangkan usaha yang dimiliki oleh para mahasiswa, sehingga dapat
            berkontribusi secara positif terhadap perekonomian lokal dan menciptakan lapangan kerja baru. Melalui P2MW,
            SEKENID diharapkan dapat menjadi sarana efektif bagi mahasiswa untuk mengasah keterampilan kewirausahaan
            mereka dan mencapai kesuksesan dalam dunia bisnis.
        </p>
        <br><br>
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <img src="assets/sekenid.png" alt="SEKENID" class="img-fluid" style="max-width: 80%;">
            </div>
            <div class="col-md-6 text-center">
                <img src="assets/P2MW.svg" alt="P2MW" class="img-fluid" style="max-width: 80%;">
            </div>
        </div>F

    </div>
    <!-- footer -->
    <footer class="footer bg-white rounded-lg shadow m-4 dark:bg-gray-900">
        <span class="block text-sm text-gray-500 sm:text-center dark:text-gray-400">© 2023 <a href="index.php"
                class="hover:underline">SEKENID</a>. All Rights Reserved.</span>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-KyZXEAg3QhqLMpG8r+Knujsl5+5hb7xD9Cxpse6/8B0=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.js"></script>
</body>

</html>