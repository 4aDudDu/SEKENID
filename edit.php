<?php
include 'koneksi.php';
session_start();
$is_logged_in = isset($_SESSION['username']);
$user = $_SESSION['id'] ?? null;

if (!$is_logged_in) {
    header('Location: login.php');
    exit();
}

$product_id = $_GET['id'] ?? null;

if ($product_id === null) {
    header('Location: laporan.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_product'])) {
    $namabarang = $_POST['namabarang'];
    $harga = $_POST['harga'];
    $kategori = $_POST['kategori'];
    $jenis = $_POST['jenis'];
    $qty = $_POST['qty']; 
    $foto = $_FILES['foto']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($foto);

    if ($foto) {
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
            $stmt = $conn->prepare("UPDATE produk SET namabarang = ?, harga = ?, kategori = ?, jenis = ?, qty = ?, foto = ? WHERE id = ? AND seller_id = ?");
            $stmt->bind_param("ssssissi", $namabarang, $harga, $kategori, $jenis, $qty, $target_file, $product_id, $user);
        } else {
            echo "Error uploading file.";
            exit();
        }
    } else {
        $stmt = $conn->prepare("UPDATE produk SET namabarang = ?, harga = ?, kategori = ?, jenis = ?, qty = ? WHERE id = ? AND seller_id = ?");
        $stmt->bind_param("ssssiii", $namabarang, $harga, $kategori, $jenis, $qty, $product_id, $user);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Produk berhasil diperbarui.'); window.location.href='laporan.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$stmt = $conn->prepare("SELECT * FROM produk WHERE id = ? AND seller_id = ?");
$stmt->bind_param("ii", $product_id, $user);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    header('Location: laporan.php');
    exit();
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk</title>
    <link rel="stylesheet" href="laporan.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <button type="button" class="btn btn-warning backbtn" id="homeButton">Kembali</button>
        <h1>Edit Produk</h1>
        <form method="POST" enctype="multipart/form-data" action="edit.php?id=<?php echo $product_id; ?>">
            <div class="mb-3">
                <label for="nama-produk" class="col-form-label">Nama Produk:</label>
                <input type="text" class="form-control" name="namabarang" id="nama-produk" value="<?php echo $product['namabarang']; ?>" required />
            </div>
            <div class="mb-3">
                <label for="kategori" class="col-form-label">Kategori:</label>
                <select class="form-select" name="kategori" id="kategori" required>
                    <option value="Kendaraan" <?php if ($product['kategori'] == 'Kendaraan') echo 'selected'; ?>>Kendaraan</option>
                    <option value="Pakaian" <?php if ($product['kategori'] == 'Pakaian') echo 'selected'; ?>>Pakaian</option>
                    <option value="Elektronik" <?php if ($product['kategori'] == 'Elektronik') echo 'selected'; ?>>Elektronik</option>
                    <option value="Tanah dan Bangunan" <?php if ($product['kategori'] == 'Tanah dan Bangunan') echo 'selected'; ?>>Tanah dan Bangunan</option>
                    <option value="Mainan Anak - Anak" <?php if ($product['kategori'] == 'Mainan Anak - Anak') echo 'selected'; ?>>Mainan Anak - Anak</option>
                    <option value="Perabotan" <?php if ($product['kategori'] == 'Perabotan') echo 'selected'; ?>>Perabotan</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="jenis" class="col-form-label">Jenis:</label>
                <select class="form-select" name="jenis" id="jenis" required>
                    <option value="Baru" <?php if ($product['jenis'] == 'Baru') echo 'selected'; ?>>Baru</option>
                    <option value="Second - Seperti Baru" <?php if ($product['jenis'] == 'Second - Seperti Baru') echo 'selected'; ?>>Second - Seperti Baru</option>
                    <option value="Second - Layak Dipakai" <?php if ($product['jenis'] == 'Second - Layak Dipakai') echo 'selected'; ?>>Second - Layak Dipakai</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="foto-barang" class="col-form-label">Foto:</label>
                <input type="file" class="form-control" name="foto" id="foto-barang" accept=".jpg, .png, .heic, .bmp" />
                <img src="<?php echo $product['foto']; ?>" alt="Current Image" class="img-fluid mt-2" style="max-height: 200px;">
            </div>
            <div class="mb-3">
                <label for="harga-barang" class="col-form-label">Harga (Rp):</label>
                <input type="number" class="form-control" name="harga" id="harga-barang" value="<?php echo $product['harga']; ?>" required />
            </div>
            <div class="mb-3">
                <label for="qty-barang" class="col-form-label">Kuantitas:</label>
                <input type="number" class="form-control" name="qty" id="qty-barang" value="<?php echo $product['qty']; ?>" required />
            </div>
            <input type="hidden" name="update_product" value="1">
            <button type="submit" class="btn btn-warning">Save</button>
        </form>
    </div>
    <script>
        document.getElementById("homeButton").addEventListener("click", function () {
            window.location.href = "index.php";
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
