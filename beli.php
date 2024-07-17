<?php
session_start();
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $produk_id = $_POST['produk_id'];
    $jumlah = $_POST['jumlah'];

    // Check product stock
    $query = "SELECT stok FROM produk WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $produk_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if ($product['stok'] < $jumlah) {
        echo "Stok tidak cukup.";
        exit();
    }

    // Reduce stock
    $new_stock = $product['stok'] - $jumlah;
    $query = "UPDATE produk SET stok = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $new_stock, $produk_id);
    $stmt->execute();

    // Implement further purchase logic here (e.g., saving purchase details to another table)

    echo "Pembelian berhasil.";
    // Redirect to a success page or order summary
    header("Location: success.php");
    exit();
}
?>
