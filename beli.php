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

// Handle form submission
$success_message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['purchase'])) {
    $nama_penerima = $_POST['nama_penerima'];
    $alamat = $_POST['alamat'];
    $nomor_hp = $_POST['nomor_hp'];
    $provinsi = $_POST['provinsi'];
    $metode_pembayaran = $_POST['metode_pembayaran'];
    $nomor_rekening = isset($_POST['nomor_rekening']) ? $_POST['nomor_rekening'] : '';

    // Process the payment and save the order details
    // You can add your payment processing logic here
    $success_message = 'Pembayaran Berhasil!';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beli Produk</title>
    <link rel="stylesheet" href="style2.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 style="text-align: center;">METODE PEMBAYARAN</h2>
    <?php if ($success_message): ?>
        <div class="alert alert-success" role="alert">
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>
    <div class="product-details mb-4">
        <h3><?php echo $product['namabarang']; ?></h3>
        <p>Kategori: <?php echo $product['kategori']; ?></p>
        <p>Jenis: <?php echo $product['jenis']; ?></p>
        <p>Harga: Rp. <?php echo number_format($product['harga'], 0, ',', '.'); ?></p>
        <img src="<?php echo $product['foto']; ?>" alt="Product Image" class="img-fluid">
    </div>
    <form method="POST" action="beli.php?id=<?php echo $product_id; ?>">
        <div class="mb-3">
            <label for="nama_penerima" class="form-label">Nama Penerima</label>
            <input type="text" class="form-control" id="nama_penerima" name="nama_penerima" required>
        </div>
        <div class="mb-3">
            <label for="alamat" class="form-label">Alamat</label><br>
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
            <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
            <select class="form-select" id="metode_pembayaran" name="metode_pembayaran" required>
                <option value="COD">Cash on Delivery (COD)</option>
                <option value="Debit">Debit</option>
            </select>
        </div>
        <div class="mb-3" id="rekening_field" style="display: none;">
            <label for="nomor_rekening" class="form-label">Nomor Rekening</label>
            <input type="text" class="form-control" id="nomor_rekening" name="nomor_rekening">
        </div>
        <button type="submit" name="purchase" class="btn btn-primary">BELI BARANG!</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        $('#metode_pembayaran').on('change', function() {
            if ($(this).val() === 'Debit') {
                $('#rekening_field').show();
            } else {
                $('#rekening_field').hide();
            }
        });
    });
</script>
</body>
</html>
