<?php
session_start(); // Start the session
require 'config.php'; // Include the database configuration

// Handle Stogranci Login
if (isset($_POST['stogranci_login'])) {
    $name = $_POST['name'];
    $ltname = $_POST['ltname'];
    $password = $_POST['password'];


    // Query to check if the user exists
    $stmt = $conn->prepare("SELECT no FROM stogranci WHERE Name = ? AND LTName = ? AND Password = ?");
    $stmt->bind_param("sss", $name, $ltname, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['loggedin'] = true;
        $_SESSION['no'] = $user['no']; // Store the user's NO in the session

        // Debugging: Check the NO
        echo "<script>console.log('Logged in as NO: {$user['no']}');</script>";

        header("Location: stogranci_dashboard.php");
        exit;
    } else {
        echo "<script>alert('Invalid credentials!'); window.location.href='index.html';</script>";
    }

    $stmt->close();
}

// Handle Stogranci Sign Up
if (isset($_POST['signup'])) {
    $name = $_POST['name'];
    $lastname = $_POST['lastname'];
    $password = $_POST['password'];
    $no = rand(99,999); // Generate a unique ID for the user

    // Insert new user into the database
    $stmt = $conn->prepare("INSERT INTO stogranci (no, name, ltname, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $no, $name, $lastname, $password);

    if ($stmt->execute()) {
        echo "<script>alert('Sign up successful! You can now login.'); window.location.href='index.html';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}

// Handle Tchogratmen Login
if (isset($_POST['tchogratmen_login'])) {
    $name = $_POST['name'];
    $password = $_POST['password'];

    // Query to check if the teacher exists
    $stmt = $conn->prepare("SELECT * FROM tchogratmen WHERE name = ? AND password = ?");
    $stmt->bind_param("ss", $name, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Teacher authenticated
        $_SESSION['loggedin'] = true;
        $_SESSION['teacher_name'] = $name; // Store the teacher's name in the session

        // Redirect to teacher dashboard
        header("Location: tchogratmen_dashboard.php");
        exit;
    } else {
        echo "<script>alert('Geçersiz kimlik bilgileri!'); window.location.href='index.html';</script>";
    }

    $stmt->close();
}