<?php
include 'koneksi.php';
session_start();
$is_logged_in = isset($_SESSION['username']);
$user = $_SESSION['id'] ?? null;

if (!$is_logged_in) {
    header('Location: login.php');
    exit();
}

$stmt = $conn->prepare("
    SELECT cr.*, 
           bu.username AS buyer_username, 
           se.username AS seller_username
    FROM chat_rooms cr
    JOIN akun bu ON cr.buyer_id = bu.id
    JOIN akun se ON cr.seller_id = se.id
    WHERE cr.buyer_id = ? OR cr.seller_id = ?
");
$stmt->bind_param("ii", $user, $user);
$stmt->execute();
$chat_rooms = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List of Chats</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="scene.css">
</head>

<body>
    <button class="backbtn btn btn-warning" id="homeButton">Kembali</button>
    <div class="chat-list-container card shadow">
        <div class="card-header chat-header">
            Your Chat Rooms
        </div>
        <div class="list-group list-group-flush">
            <?php while ($row = $chat_rooms->fetch_assoc()): ?>
                <a href="chat.php?room_id=<?php echo $row['room_id']; ?>" class="list-group-item list-group-item-action d-flex align-items-center">
                    <div class="chat-avatar"></div>
                    <div class="chat-info flex-grow-1">
                        <div class="chat-name">
                            <?php echo $row['buyer_id'] == $user ? 'Seller ' . $row['seller_username'] : 'Buyer ' . $row['buyer_username']; ?>
                        </div>
                        <div class="chat-preview">Pesan</div>
                    </div>
                </a>
            <?php endwhile; ?>
        </div>
    </div>
    <script>
        document.getElementById("homeButton").addEventListener("click", function () {
            window.location.href = "index.php";
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
