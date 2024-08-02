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
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@1.6.2/dist/flowbite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/flowbite@1.6.2/dist/flowbite.bundle.js"></script>
</head>
<body>
    <div class="container mx-auto p-4">
        <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-lg">
            <h2 class="text-2xl font-semibold mb-4 text-center">Pembelian Produk</h2>

            <?php if ($success_message): ?>
                <div class="alert alert-success mb-4">
                    <?php echo $success_message; ?>
                </div>
            <?php elseif ($error_message): ?>
                <div class="alert alert-danger mb-4">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <div class="flex flex-col md:flex-row gap-4">
                <!-- Product Details -->
                <div class="md:w-1/2">
                    <div class="card">
                        <img src="<?php echo $product['foto']; ?>" class="w-full h-60 object-cover rounded-t-lg" alt="Product Image">
                        <div class="p-4">
                            <h3 class="text-xl font-semibold"><?php echo $product['namabarang']; ?></h3>
                            <p class="text-gray-600">Kategori: <?php echo $product['kategori']; ?></p>
                            <p class="text-gray-600">Jenis: <?php echo $product['jenis']; ?></p>
                            <p class="text-gray-800 font-bold">Harga: Rp. <?php echo number_format($product['harga'], 0, ',', '.'); ?></p>
                            <p class="text-gray-800 font-bold">Stok: <?php echo $product['qty']; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Form Section -->
                <div class="md:w-1/2">
                    <form method="POST" action="beli.php?id=<?php echo $product_id; ?>">
                        <div class="mb-4">
                            <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
                            <textarea class="form-textarea mt-1 block w-full" id="alamat" name="alamat" rows="3" required></textarea>
                        </div>
                        <div class="mb-4">
                            <label for="nomor_hp" class="block text-sm font-medium text-gray-700">Nomor HP</label>
                            <input type="text" class="form-input mt-1 block w-full" id="nomor_hp" name="nomor_hp" required>
                        </div>
                        <div class="mb-4">
                            <label for="provinsi" class="block text-sm font-medium text-gray-700">Provinsi</label>
                            <input type="text" class="form-input mt-1 block w-full" id="provinsi" name="provinsi" required>
                        </div>
                        <div class="mb-4">
                            <label for="jumlah" class="block text-sm font-medium text-gray-700">Jumlah</label>
                            <input type="number" class="form-input mt-1 block w-full" id="jumlah" name="jumlah" required>
                        </div>
                        <button type="submit" name="purchase" class="btn btn btn-warning">Beli Barang!</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
