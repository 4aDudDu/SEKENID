<?php
include 'koneksi.php';
session_start();
$is_logged_in = isset($_SESSION['username']);
$user = $_SESSION['id'] ?? null;

if (!$is_logged_in) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_product'])) {
        $product_id = $_POST['product_id'];
        $stmt = $conn->prepare("DELETE FROM produk WHERE id = ? AND seller_id = ?");
        $stmt->bind_param("ii", $product_id, $user);
        $stmt->execute();
    } elseif (isset($_POST['complete_order'])) {
        $order_id = $_POST['order_id'];
        $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
    } elseif (isset($_POST['chat'])) {
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
}

$stmt = $conn->prepare("SELECT * FROM produk WHERE seller_id = ?");
$stmt->bind_param("i", $user);
$stmt->execute();
$result = $stmt->get_result();

// Fetch orders and corresponding chat rooms
$order_stmt = $conn->prepare("SELECT o.id, p.namabarang, o.jumlah, a.username AS pembeli, o.alamat, o.nomor_hp, o.provinsi, o.created_at, cr.room_id, o.product_id, o.seller_id 
FROM orders o 
JOIN produk p ON o.product_id = p.id 
JOIN akun a ON o.buyer_id = a.id 
LEFT JOIN chat_rooms cr ON o.buyer_id = cr.buyer_id AND o.seller_id = cr.seller_id
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
                    echo "<p class='card-text'>Waktu Pemesanan: " . $order['created_at'] . "</p>";
                    echo "<form method='POST' action='laporan.php' class='d-inline'>";
                    echo "<input type='hidden' name='product_id' value='" . $order['product_id'] . "'>";
                    echo "<input type='hidden' name='seller_id' value='" . $order['seller_id'] . "'>";
                    echo "<button type='submit' name='chat' class='btn btn-primary'>Chat</button>";
                    echo "</form> ";
                    echo "<form method='POST' action='laporan.php' class='d-inline'>";
                    echo "<input type='hidden' name='order_id' value='" . $order['id'] . "'>";
                    echo "<button type='submit' name='complete_order' class='btn btn-success'>Pesanan Selesai</button>";
                    echo "</form>";
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
