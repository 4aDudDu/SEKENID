<?php
include 'koneksi.php';
session_start();
$is_logged_in = isset($_SESSION['username']);

if (!$is_logged_in) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_product'])) {
    $namabarang = $_POST['namabarang'];
    $harga = $_POST['harga'];
    $created_date = date('Y-m-d');
    $kategori = $_POST['kategori'];
    $jenis = $_POST['jenis'];
    $foto = $_FILES['foto']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($foto);

    // Upload file
    if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO produk (namabarang, harga, created_date, kategori, jenis, foto) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $namabarang, $harga, $created_date, $kategori, $jenis, $target_file);

        if ($stmt->execute()) {
            echo "<script>alert('Produk berhasil ditambahkan.'); window.location.href='index.php';</script>";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error uploading file.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk</title>
    <link rel="stylesheet" href="style.css">
    <link rel="website icon" type="png" href="assets/sekenid.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Tambah Produk</h1>
        <form method="POST" enctype="multipart/form-data" action="jual.php">
            <div class="mb-3">
                <label for="nama-produk" class="col-form-label">Nama Produk:</label>
                <input type="text" class="form-control" name="namabarang" id="nama-produk" required />
            </div>
            <div class="mb-3">
                <label for="kategori" class="col-form-label">Kategori:</label>
                <select class="form-select" name="kategori" id="kategori" required>
                    <option value="Kendaraan">Kendaraan</option>
                    <option value="Pakaian">Pakaian</option>
                    <option value="Elektronik">Elektronik</option>
                    <option value="Tanah dan Bangunan">Tanah dan Bangunan</option>
                    <option value="Mainan Anak - Anak">Mainan Anak - Anak</option>
                    <option value="Perabotan">Perabotan</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="jenis" class="col-form-label">Jenis:</label>
                <select class="form-select" name="jenis" id="jenis" required>
                    <option value="Baru">Baru</option>
                    <option value="Second - Seperti Baru">Second - Seperti Baru</option>
                    <option value="Second - Layak Dipakai">Second - Layak Dipakai</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="foto-barang" class="col-form-label">Foto:</label>
                <input type="file" class="form-control" name="foto" id="foto-barang" accept=".jpg, .png, .heic, .bmp" required />
            </div>
            <div class="mb-3">
                <label for="harga-barang" class="col-form-label">Harga (Rp):</label>
                <input type="number" class="form-control" name="harga" id="harga-barang" required />
            </div>
            <input type="hidden" name="submit_product" value="1">
            <button type="submit" class="btn btn-warning">Save</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
