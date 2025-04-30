<?php
session_start(); // Start the session
require 'config.php'; // Include the database configuration
require 'phpwordw/vendor/autoload.php'; // Include PHPWord autoloader

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

// Check if the user is logged in
if (!isset($_SESSION['loggedin'])) {
    header("Location: index.html");
    exit;
}

// Fetch the logged-in student's NO from the session
$logged_in_no = $_SESSION['no']; // Retrieve the receiver_no from the session

// Fetch messages for the logged-in student with sender's name, last name, and NO
$sql = "SELECT m.message, s.name AS sender_name, s.ltname AS sender_lastname, s.no AS sender_no 
        FROM messages m
        JOIN stogranci s ON m.sender_no = s.no
        WHERE m.receiver_no = ?
        ORDER BY m.timestamp DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $logged_in_no);
$stmt->execute();
$result = $stmt->get_result();

// Handle Word document generation
if (isset($_POST['generate_word'])) {
    $phpWord = new PhpWord();

    // Add a section to the document
    $section = $phpWord->addSection();

    // Add a title
    $section->addText('Mezuniyet mesajları', ['name' => 'Arial', 'size' => 16, 'bold' => true]);

    // Add messages to the document
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $section->addText("itibaren: {$row['sender_name']} {$row['sender_lastname']} (NO: {$row['sender_no']})", ['name' => 'Arial', 'size' => 12, 'bold' => true]);
            $section->addText($row['message'], ['name' => 'Arial', 'size' => 12]);
            $section->addTextBreak(); // Add a line break between messages
        }
    } else {
        $section->addText('Boş.', ['name' => 'Arial', 'size' => 12]);
    }

    // Save the document
    $filename = 'Messaglar' . date('Y-m-d') . '.docx';
    header("Content-Description: File Transfer");
    header("Content-Disposition: attachment; filename=$filename");
    header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
    $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
    $objWriter->save('php://output');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mezuniyet Sayfası</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <a href="stogranci_dashboard.php">Sınıf</a>
        <a class="logout" href="logout.php">Oturumu kapat</a>    
    </div>

    <!-- Graduation Page -->
    <div class="container">
        <h2>Mezuniyet Sayfası</h2>
        <form method="post" action="">
            <button type="submit" name="generate_word">Mesajları Word Olarak İndir</button>
        </form>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='message'>
                        <div class='sender'>itibaren: {$row['sender_name']} {$row['sender_lastname']} NO: {$row['sender_no']}</div>
                        <div class='message-text'>{$row['message']}</div>
                      </div>";
            }
        } else {
            echo "<p>Hiçbir mesaj bulunamadı.</p>";
        }
        ?>
    </div>
</body>
</html>