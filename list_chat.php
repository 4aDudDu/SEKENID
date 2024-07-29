<?php
include 'koneksi.php';
session_start();
$is_logged_in = isset($_SESSION['username']);
$user = $_SESSION['id'] ?? null;

if (!$is_logged_in) {
    header('Location: login.php');
    exit();
}

$stmt = $conn->prepare("SELECT * FROM chat_rooms WHERE buyer_id = ? OR seller_id = ?");
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
    <link rel="stylesheet" href="scene.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.css" rel="stylesheet" />
</head>

<body>
    <button class="backbtn btn btn-warning" id="homeButton">Kembali</button>
    <div class="chat-list-container">
        <div class="chat-header">
            Your Chat Rooms
        </div>
        <div class="list-group">
            <?php while ($row = $chat_rooms->fetch_assoc()): ?>
                <a href="chat.php?room_id=<?php echo $row['room_id']; ?>" class="list-group-item list-group-item-action">
                    <div class="chat-avatar"></div>
                    <div class="chat-info">
                        <div class="chat-name">
                            <?php echo $row['buyer_id'] == $user ? 'Seller ' . $row['seller_id'] : 'Buyer ' . $row['buyer_id']; ?>
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
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>