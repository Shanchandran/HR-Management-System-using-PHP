<?php
session_start(); // Start session to track login status
$conn = new mysqli("localhost", "root", "", "hrms"); // Database connection

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get input from login form
$email = trim($_POST['email']);
$password = trim($_POST['password']);

// Password validation pattern
$passwordPattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";

if (!preg_match($passwordPattern, $password)) {
    echo "<script>alert('Password must be at least 8 characters with at least 1 uppercase letter, 1 lowercase letter, 1 number, and 1 special character.'); window.location.href='login.html';</script>";
    exit();
}

// Prepare SQL query (strict checking)
$stmt = $conn->prepare("SELECT * FROM admin_user WHERE Email = ? AND Password = ?");
$stmt->bind_param("ss", $email, $password);
$stmt->execute();
$result = $stmt->get_result();

// Check if credentials match
if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $_SESSION['admin_name'] = $row['Name']; // Store session variable
    $_SESSION['ID'] = $row['ID']; // Store admin ID in session
    echo "<script>alert('Login successful!'); window.location.href='admin_dashboard.php';</script>"; // Redirect to dashboard
} else {
    echo "<script>alert('Invalid email or password!'); window.location.href='login.html';</script>"; // Stay on login page
}

$stmt->close();
$conn->close();
?>