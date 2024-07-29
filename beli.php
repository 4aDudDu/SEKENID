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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@1.6.2/dist/flowbite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/flowbite@1.6.2/dist/flowbite.bundle.js"></script>
</head>



<body>
    <div class="container mx-auto p-4">
        <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-lg">
            <h2 class="text-2xl font-semibold mb-4 text-center">Metode Pembayaran</h2>

            <?php if ($success_message): ?>
                <div class="alert alert-success mb-4">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <div class="flex flex-col md:flex-row gap-4">
                <!-- Product Details -->
                <div class="md:w-1/2">
                    <div class="card">
                        <img src="<?php echo $product['foto']; ?>" class="w-full h-60 object-cover rounded-t-lg"
                            alt="Product Image">
                        <div class="p-4">
                            <h3 class="text-xl font-semibold"><?php echo $product['namabarang']; ?></h3>
                            <p class="text-gray-600">Kategori: <?php echo $product['kategori']; ?></p>
                            <p class="text-gray-600">Jenis: <?php echo $product['jenis']; ?></p>
                            <p class="text-gray-800 font-bold">Harga: Rp.
                                <?php echo number_format($product['harga'], 0, ',', '.'); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Form Section -->
                <div class="md:w-1/2">
                    <form method="POST" action="beli.php?id=<?php echo $product_id; ?>">
                        <div class="mb-4">
                            <label for="nama_penerima" class="block text-sm font-medium text-gray-700">Nama
                                Penerima</label>
                            <input type="text" class="form-input mt-1 block w-full" id="nama_penerima"
                                name="nama_penerima" required>
                        </div>
                        <div class="mb-4">
                            <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
                            <textarea class="form-textarea mt-1 block w-full" id="alamat" name="alamat" rows="3"
                                required></textarea>
                        </div>
                        <div class="mb-4">
                            <label for="nomor_hp" class="block text-sm font-medium text-gray-700">Nomor HP</label>
                            <input type="text" class="form-input mt-1 block w-full" id="nomor_hp" name="nomor_hp"
                                required>
                        </div>
                        <div class="mb-4">
                            <label for="provinsi" class="block text-sm font-medium text-gray-700">Provinsi</label>
                            <input type="text" class="form-input mt-1 block w-full" id="provinsi" name="provinsi"
                                required>
                        </div>
                        <div class="mb-4">
                            <label for="metode_pembayaran" class="block text-sm font-medium text-gray-700">Metode
                                Pembayaran</label>
                            <select class="form-select mt-1 block w-full" id="metode_pembayaran"
                                name="metode_pembayaran" required>
                                <option value="COD">Cash on Delivery (COD)</option>
                                <option value="Debit">Debit</option>
                            </select>
                        </div>
                        <div class="mb-4" id="rekening_field" style="display: none;">
                            <label for="nomor_rekening" class="block text-sm font-medium text-gray-700">Nomor
                                Rekening</label>
                            <input type="text" class="form-input mt-1 block w-full" id="nomor_rekening"
                                name="nomor_rekening">
                        </div>
                        <button type="submit" name="purchase" class="btn btn btn-warning">Beli Barang!</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const metodePembayaran = document.getElementById('metode_pembayaran');
            const rekeningField = document.getElementById('rekening_field');

            metodePembayaran.addEventListener('change', function () {
                if (metodePembayaran.value === 'Debit') {
                    rekeningField.style.display = 'block';
                } else {
                    rekeningField.style.display = 'none';
                }
            });
        });
    </script>
</body>

</html>