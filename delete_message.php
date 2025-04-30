<?php
session_start(); // Start the session
require 'config.php'; // Include the database configuration


if (!isset($_SESSION['loggedin'])) {
    header("Location: tchogratmen_index.html");
    exit;
}

// Check if the message ID is provided
if (isset($_POST['message_id'])) {
    $message_id = $_POST['message_id'];

    // Prepare a delete statement
    $sql = "DELETE FROM messages WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $message_id);
        if ($stmt->execute()) {
            // Message deleted successfully
            header("Location: tchogratmen_dashboard.php");
            exit;
        } else {
            echo "Error deleting message.";
        }
        $stmt->close();
    } else {
        echo "Error preparing statement.";
    }
} else {
    echo "No message ID provided.";
}

$conn->close();
?>