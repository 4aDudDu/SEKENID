<?php
include 'koneksi.php';
session_start();
$is_logged_in = isset($_SESSION['username']);
$user = $_SESSION['id'] ?? null;

if (!$is_logged_in) {
    header('Location: login.php');
    exit();
}

$room_id = $_GET['room_id'] ?? null;

if ($room_id) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])) {
        $message = $_POST['message'];
        $stmt = $conn->prepare("INSERT INTO chat_messages (room_id, sender_id, message) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $room_id, $user, $message);
        $stmt->execute();
    }

    $stmt = $conn->prepare("SELECT * FROM chat_messages WHERE room_id = ?");
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $messages = $stmt->get_result();
} else {
    echo "Room ID not specified.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Room</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="chat-container">
        <div class="chat-box">
            <?php while ($row = $messages->fetch_assoc()): ?>
                <div class="chat-message">
                    <strong><?php echo $row['sender_id'] == $user ? 'You' : 'Other'; ?>:</strong>
                    <?php echo $row['message']; ?>
                </div>
            <?php endwhile; ?>
        </div>
        <form method="POST" action="chat.php?room_id=<?php echo $room_id; ?>">
            <input type="text" name="message" placeholder="Type your message" required>
            <button type="submit">Send</button>
        </form>
    </div>
</body>

</html>