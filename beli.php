<?php
include 'koneksi.php';
session_start();

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$product_id = $_GET['id'];

// Fetch product details from the database
$stmt = $conn->prepare("SELECT * FROM produk WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    echo "Product not found.";
    exit();
}

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['purchase'])) {
    $alamat = $_POST['alamat'];
    $nomor_hp = $_POST['nomor_hp'];
    $provinsi = $_POST['provinsi'];
    $jumlah = $_POST['jumlah'];
    $buyer_id = $_SESSION['id'];
    $seller_id = $product['seller_id'];

    // Check product quantity
    if ($jumlah > $product['qty']) {
        $error_message = 'Barang yang dipesan tidak cukup.';
    } else {
        // Update product quantity
        $new_qty = $product['qty'] - $jumlah;
        $stmt = $conn->prepare("UPDATE produk SET qty = ? WHERE id = ?");
        $stmt->bind_param("ii", $new_qty, $product_id);
        $stmt->execute();

        // Insert order details into orders table
        $stmt = $conn->prepare("INSERT INTO orders (product_id, buyer_id, seller_id, alamat, nomor_hp, provinsi, jumlah, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("iiisssi", $product_id, $buyer_id, $seller_id, $alamat, $nomor_hp, $provinsi, $jumlah);
        $stmt->execute();

        $success_message = 'Pesanan Berhasil!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beli Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="laporan.css">
</head>
<body>
    <div class="container mt-5">
        <button type="button" class="btn btn-warning backbtn" id="homeButton">Kembali</button>
        <h1 class="text-center judul">Pembelian Produk</h1>

        <?php if ($success_message): ?>
            <div class="alert alert-success mb-4">
                <?php echo $success_message; ?>
            </div>
        <?php elseif ($error_message): ?>
            <div class="alert alert-danger mb-4">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Product Details -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <img src="<?php echo $product['foto']; ?>" class="card-img-top" alt="Product Image">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $product['namabarang']; ?></h5>
                        <p class="card-text">Kategori: <?php echo $product['kategori']; ?></p>
                        <p class="card-text">Jenis: <?php echo $product['jenis']; ?></p>
                        <p class="card-text">Harga: Rp. <?php echo number_format($product['harga'], 0, ',', '.'); ?></p>
                        <p class="card-text">Stok: <?php echo $product['qty']; ?></p>
                    </div>
                </div>
            </div>

            <!-- Form Section -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <form method="POST" action="beli.php?id=<?php echo $product_id; ?>">
                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat</label>
                                <textarea class="form-control" id="alamat" name="alamat" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="nomor_hp" class="form-label">Nomor HP</label>
                                <input type="text" class="form-control" id="nomor_hp" name="nomor_hp" required>
                            </div>
                            <div class="mb-3">
                                <label for="provinsi" class="form-label">Provinsi</label>
                                <input type="text" class="form-control" id="provinsi" name="provinsi" required>
                            </div>
                            <div class="mb-3">
                                <label for="jumlah" class="form-label">Jumlah</label>
                                <input type="number" class="form-control" id="jumlah" name="jumlah" required>
                            </div>
                            <button type="submit" name="purchase" class="btn btn-warning">Beli Barang!</button>
                        </form>
                    </div>
                </div>
            </div>
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
