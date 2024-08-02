<?php
include 'koneksi.php';
session_start();
$is_logged_in = isset($_SESSION['username']);
$user = $_SESSION['id'] ?? null;

if (!$is_logged_in) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_product'])) {
    $product_id = $_POST['product_id'];
    $stmt = $conn->prepare("DELETE FROM produk WHERE id = ? AND seller_id = ?");
    $stmt->bind_param("ii", $product_id, $user);
    $stmt->execute();
}

$stmt = $conn->prepare("SELECT * FROM produk WHERE seller_id = ?");
$stmt->bind_param("i", $user);
$stmt->execute();
$result = $stmt->get_result();

// Fetch orders
$order_stmt = $conn->prepare("SELECT p.namabarang, o.jumlah, a.username AS pembeli, o.alamat, o.nomor_hp, o.provinsi 
FROM orders o 
JOIN produk p ON o.product_id = p.id 
JOIN akun a ON o.buyer_id = a.id 
WHERE p.seller_id = ?");
$order_stmt->bind_param("i", $user);
$order_stmt->execute();
$order_result = $order_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="laporan.css">
</head>

<body>
    
    <div class="container mt-5">
        <button type="button" class="btn btn-warning backbtn" id="homeButton">Kembali</button>
        <h1 class="text-center judul">Laporan Produk</h1>
        <div class="row mb-5">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='col-md-4 mb-4'>";
                    echo "<div class='card h-100'>";
                    echo "<img src='" . $row['foto'] . "' class='card-img-top' alt='Product Image'>";
                    echo "<div class='card-body'>";
                    echo "<h5 class='card-title'>" . $row['namabarang'] . "</h5>";
                    echo "<p class='card-text'>Kategori: " . $row['kategori'] . " (" . $row['jenis'] . ")</p>";
                    echo "<p class='card-text'>Rp. " . number_format($row['harga'], 0, ',', '.') . "</p>";
                    echo "<a href='edit.php?id=" . $row['id'] . "' class='btn btn-primary'>Edit</a> ";
                    echo "<form method='POST' action='laporan.php' class='d-inline'>";
                    echo "<input type='hidden' name='product_id' value='" . $row['id'] . "'>";
                    echo "<button type='submit' name='delete_product' class='btn btn-danger'>Delete</button>";
                    echo "</form>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<p class='text-center'>Tidak ada produk yang ditemukan.</p>";
            }
            ?>
        </div>

        <h2 class="text-center judul">Pesanan Masuk</h2>
        <div class="row">
            <?php
            if ($order_result->num_rows > 0) {
                while ($order = $order_result->fetch_assoc()) {
                    echo "<div class='col-md-4 mb-4'>";
                    echo "<div class='card h-100'>";
                    echo "<div class='card-body'>";
                    echo "<h5 class='card-title'>Pesanan untuk: " . $order['namabarang'] . "</h5>";
                    echo "<p class='card-text'>Jumlah: " . $order['jumlah'] . "</p>";
                    echo "<p class='card-text'>Nama Pembeli: " . $order['pembeli'] . "</p>";
                    echo "<p class='card-text'>Alamat: " . $order['alamat'] . "</p>";
                    echo "<p class='card-text'>Nomor HP: " . $order['nomor_hp'] . "</p>";
                    echo "<p class='card-text'>Provinsi: " . $order['provinsi'] . "</p>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<p class='text-center'>Tidak ada pesanan yang masuk.</p>";
            }
            ?>
        </div>
    </div>
    <script>
        document.getElementById("homeButton").addEventListener("click", function () {
            window.location.href = "index.php";
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
