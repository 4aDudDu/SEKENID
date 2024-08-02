<?php
include 'koneksi.php';

$provinsi_id = $_GET['provinsi_id'];

if (!$provinsi_id) {
    echo json_encode(["error" => "No province ID received"]);
    exit();
}

$sql = "SELECT id, nama_kota FROM kota WHERE provinsi_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $provinsi_id);
$stmt->execute();
$result = $stmt->get_result();

$cities = array();
while($row = $result->fetch_assoc()) {
    $cities[] = $row;
}

echo json_encode($cities);

$stmt->close();
$conn->close();
?>
