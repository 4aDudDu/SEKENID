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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="scene.css">
</head>

<body class="bg-light">
    <div class="container my-4">
        <button class="backbtn btn btn-warning mb-3" id="homeButton">Kembali</button>
        <div class="chat-container card shadow-sm">
            <div class="card-body chat-box">
                <?php while ($row = $messages->fetch_assoc()): ?>
                    <div class="chat-message <?php echo $row['sender_id'] == $user ? 'you' : 'other'; ?> mb-2">
                        <strong><?php echo $row['sender_id'] == $user ? 'You' : 'Other'; ?>:</strong>
                        <?php echo $row['message']; ?>
                    </div>
                <?php endwhile; ?>
            </div>
            <div class="card-footer">
                <form class="d-flex" method="POST" action="chat.php?room_id=<?php echo $room_id; ?>">
                    <input type="text" name="message" class="form-control me-2" placeholder="Type your message" required>
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" style="color: black;">Kirim</button>
                </form>
            </div>
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
