<?php
session_start();
require 'config.php';

// Check if the teacher is logged in
if (!isset($_SESSION['loggedin'])) {
    header("Location: index.html"); // Redirect to the login page if not logged in
    exit;
}

$sql = "SELECT m.id, m.message, m.timestamp, 
               s1.name AS sender_name, s1.ltname AS sender_lastname, s1.no AS sender_no,
               s2.name AS receiver_name, s2.ltname AS receiver_lastname, s2.no AS receiver_no
        FROM messages m
        JOIN stogranci s1 ON m.sender_no = s1.no
        JOIN stogranci s2 ON m.receiver_no = s2.no
        ORDER BY m.timestamp DESC";
$result = $conn->query($sql);

$teacherName = $_SESSION['teacher_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Öğretmen Kontrol Paneli</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Add your CSS styles here */
        .message {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .message .sender, .message .receiver {
            font-weight: bold;
            color: #6a11cb;
        }
        .message .timestamp {
            color: #777;
            font-size: 0.9em;
        }
        .delete-btn {
            background-color: #ff4d4d;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
        }
        .delete-btn:hover {
            background-color: #cc0000;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <a href="students.php">Öğrenciler</a>
        <a href="tchogratmen_dashboard.php" class="active">Tüm Mesajlar</a>
        <a class="logout" href="logout.php">Oturumu kapat</a>    
    </div>

    <!-- Teacher Dashboard -->
    <div class="container">
        <h2>Öğretmen Kontrol Paneli</h2>
        <h3>Hoş geldiniz, <?php echo $teacherName; ?>!</h3>
        <h3>Tüm Mesajlar</h3>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='message'>
                        <div class='sender'>itibaren: {$row['sender_name']} {$row['sender_lastname']} (NO: {$row['sender_no']})</div>
                        <div class='receiver'>İle: {$row['receiver_name']} {$row['receiver_lastname']} (NO: {$row['receiver_no']})</div>
                        <div class='message-text'>{$row['message']}</div>
                        <div class='timestamp'>{$row['timestamp']}</div>
                        <form method='POST' action='delete_message.php' style='display:inline;'>
                            <input type='hidden' name='message_id' value='{$row['id']}'>
                            <button type='submit' class='delete-btn'>Silmek</button>
                        </form>
                      </div>";
            }
        } else {
            echo "<p>Mesajlar bulunamadı.</p>";
        }
        ?>
    </div>
</body>
</html>