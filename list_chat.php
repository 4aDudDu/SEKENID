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
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="chat-list-container">
        <h2>Your Chat Rooms</h2>
        <ul>
            <?php while ($row = $chat_rooms->fetch_assoc()): ?>
                <li>
                    <a href="chat.php?room_id=<?php echo $row['room_id']; ?>">
                        Chat with
                        <?php echo $row['buyer_id'] == $user ? 'Seller ' . $row['seller_id'] : 'Buyer ' . $row['buyer_id']; ?>
                    </a>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
</body>

</html> 