<?php
session_start(); // Start the session
require 'config.php';

// Check if the teacher is logged in
if (!isset($_SESSION['loggedin'])) {
    header("Location: tchogratmen_index.html");
    exit;
}

// Fetch all students
$sql = "SELECT no, name, ltname FROM stogranci";
$result = $conn->query($sql);

// Handle student deletion
if (isset($_POST['delete_student'])) {
    $student_no = $_POST['student_no'];
    $delete_sql = "DELETE FROM stogranci WHERE no = ?";
    if ($stmt = $conn->prepare($delete_sql)) {
        $stmt->bind_param("i", $student_no);
        if ($stmt->execute()) {
            // Student deleted successfully
            header("Location: students.php");
            exit;
        } else {
            echo "Error deleting student.";
        }
        $stmt->close();
    } else {
        echo "Error preparing statement.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Students</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Add your CSS styles here */
        .student {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .student .name {
            font-weight: bold;
            color: #6a11cb;
        }
        .student .no {
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
        <a href="tchogratmen_dashboard.php">Tüm Mesajlar</a>
        <a href="students.php" class="active">Öğrenciler</a>
        <a class="logout" href="logout.php">Oturumu kapat</a>    
    </div>

    <!-- Students Management -->
    <div class="container">
        <h2>Öğrencileri Yönet</h2>
        <h3>Tüm Öğrenciler</h3>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='student'>
                        <div class='name'>{$row['name']} {$row['ltname']}</div>
                        <div class='no'>Student NO: {$row['no']}</div>
                        <form method='POST' action='students.php' style='display:inline;'>
                            <input type='hidden' name='student_no' value='{$row['no']}'>
                            <button type='submit' name='delete_student' class='delete-btn'>Silmek</button>
                        </form>
                      </div>";
            }
        } else {
            echo "<p>Hiçbir öğrenci bulunamadı.</p>";
        }
        ?>
    </div>
</body>
</html>