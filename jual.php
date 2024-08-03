<?php
include 'koneksi.php';
session_start();
$is_logged_in = isset($_SESSION['username']);
$user = $_SESSION['id'] ?? null;

if (!$is_logged_in) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $namabarang = $_POST['namabarang'];
    $harga = $_POST['harga'];
    $kategori = $_POST['kategori'];
    $jenis = $_POST['jenis'];
    $qty = $_POST['qty'];
    $foto = $_FILES['foto']['name'];
    $provinsi = $_POST['provinsi'];
    $kota = $_POST['kota'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($foto);

    if ($foto) {
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
            $stmt = $conn->prepare("INSERT INTO produk (namabarang, harga, kategori, jenis, qty, foto, seller_id, provinsi, kota) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssissis", $namabarang, $harga, $kategori, $jenis, $qty, $target_file, $user, $provinsi, $kota);
        } else {
            echo "Error uploading file.";
            exit();
        }
    } else {
        $stmt = $conn->prepare("INSERT INTO produk (namabarang, harga, kategori, jenis, qty, seller_id, provinsi, kota) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssisss", $namabarang, $harga, $kategori, $jenis, $qty, $user, $provinsi, $kota);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Produk berhasil ditambahkan.'); window.location.href='index.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Ambil data provinsi
$provinsi_result = $conn->query("SELECT id, nama_provinsi FROM provinsi");
$provinsi_options = '';
while ($row = $provinsi_result->fetch_assoc()) {
    $provinsi_options .= "<option value='{$row['id']}'>{$row['nama_provinsi']}</option>";
}

// Ambil data kota
$kota_result = $conn->query("SELECT id, nama_kota FROM kota");
$kota_options = '';
while ($row = $kota_result->fetch_assoc()) {
    $kota_options .= "<option value='{$row['id']}'>{$row['nama_kota']}</option>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jual Produk</title>
    <link rel="stylesheet" href="laporan.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <button type="button" class="btn btn-warning backbtn" id="homeButton">Kembali</button>
        <h1>Jual Produk</h1>
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
                <label for="provinsi" class="col-form-label">Provinsi:</label>
                <select class="form-select" name="provinsi" id="provinsi" required>
                    <?php echo $provinsi_options; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="kota" class="col-form-label">Kota:</label>
                <select class="form-select" name="kota" id="kota" required>
                    <?php echo $kota_options; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="foto-barang" class="col-form-label">Foto:</label>
                <input type="file" class="form-control" name="foto" id="foto-barang" accept=".jpg, .png, .heic, .bmp" />
            </div>
            <div class="mb-3">
                <label for="harga-barang" class="col-form-label">Harga (Rp):</label>
                <input type="number" class="form-control" name="harga" id="harga-barang" required />
            </div>
            <div class="mb-3">
                <label for="qty-barang" class="col-form-label">Kuantitas:</label>
                <input type="number" class="form-control" name="qty" id="qty-barang" required />
            </div>
            <input type="hidden" name="add_product" value="1">
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
