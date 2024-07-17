<?php
include 'koneksi.php';
session_start();

// Ambil ID produk dari URL
$product_id = isset($_GET['id']) ? $_GET['id'] : null;

if ($product_id) {
    // Kueri untuk mendapatkan detail produk dan nama penjual
    $stmt = $conn->prepare("SELECT produk.*, akun.nama as penjual FROM produk 
                            JOIN produk_akun ON produk.id = produk_akun.id_produk 
                            JOIN akun ON produk_akun.id_akun = akun.id 
                            WHERE produk.id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Periksa apakah produk ditemukan
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        echo "Produk tidak ditemukan.";
        exit;
    }
} else {
    echo "ID produk tidak valid.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Produk</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h1 class="mt-4"><?php echo $product['namabarang']; ?></h1>
        <div class="row">
            <div class="col-md-6">
                <img src="<?php echo $product['foto']; ?>" class="img-fluid" alt="Product Image">
            </div>
            <div class="col-md-6">
                <h3>Rp. <?php echo number_format($product['harga'], 0, ',', '.'); ?></h3>
                <p>Kategori: <?php echo $product['kategori']; ?></p>
                <p>Jenis: <?php echo $product['jenis']; ?></p>
                <p>Nama Penjual: <?php echo $product['penjual']; ?></p>
                <form action="beli.php" method="post">
                    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                    <div class="mb-3">
                        <label for="jumlah" class="form-label">Jumlah</label>
                        <input type="number" class="form-control" id="jumlah" name="jumlah" value="1" min="1" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Beli</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>