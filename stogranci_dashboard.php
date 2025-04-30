<?php
session_start();
require 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['loggedin'])) {
    header("Location: index.html");
    exit;
}

$logged_in_no = $_SESSION['no'];

if (empty($logged_in_no)) {
    die("Sender NO is not set in the session. Please log in again.");
}

// Fetch all students from the stogranci table EXCEPT the logged-in user
$sql = "SELECT no, name, ltname FROM stogranci WHERE no != ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $logged_in_no);
$stmt->execute();
$result = $stmt->get_result();

// Handle sending a message
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_message'])) {
    $sender_no = $logged_in_no; // Sender is the logged-in student
    $receiver_no = $_POST['receiver_no'];
    $message = $_POST['message'];

    // Insert the message into the messages table
    $stmt = $conn->prepare("INSERT INTO messages (sender_no, receiver_no, message) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $sender_no, $receiver_no, $message);

    if ($stmt->execute()) {
        echo "<script>alert('Message sent successfully!');</script>";
    } else {
        echo "<script>alert('Error sending message.');</script>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Mezuniyet Sayfası</title>
    <link rel="stylesheet" href="style.css">
    <script>
        localStorage.setItem('loggedInNo', '<?php echo $logged_in_no; ?>');

        // Function to open the send message dialog
        function openDialog(receiverNo) {
            document.getElementById('receiver_no').value = receiverNo;
            document.getElementById('messageDialog').style.display = 'block';
        }

        // Function to close the dialog
        function closeDialog() {
            document.getElementById('messageDialog').style.display = 'none';
        }
    </script>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <a href="graduation_page.php">Mezuniyet Sayfası</a>
        <a class="logout" href="logout.php">Oturumu kapat</a>
    </div>

    <!-- Class Section -->
    <div class="container">
        <h2>Sınıf</h2>
        <table>
            <thead>
                <tr>
                    <th>NO</th>
                    <th>Adı</th>
                    <th>soyadı</th>
                    <th>eylemler</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['no']}</td>
                                <td>{$row['name']}</td>
                                <td>{$row['ltname']}</td>
                                <td><button onclick='openDialog(\"{$row['no']}\")'>Mesaj Gönder</button></td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>Hiçbir öğrenci bulunamadı.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Send Message Dialog -->
    <div id="messageDialog" class="dialog">
        <h3>Mesaj Gönder</h3>
        <form method="post" action="">
            <input type="hidden" id="receiver_no" name="receiver_no">
            <textarea name="message" placeholder="Mesajınızı buraya yazın..." required></textarea>
            <button type="submit" name="send_message">Mesaj Gönder</button>
            <button type="button" onclick="closeDialog()">İptal etmek</button>
        </form>
    </div>
</body>
</html>