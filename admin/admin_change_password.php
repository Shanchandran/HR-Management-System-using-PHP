<?php
// Start the session
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "hrms");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$error_message = ""; // Initialize error message
$success_message = ""; // Initialize success message

// Fetch the current admin's ID from the session (assuming it's stored during login)
if (!isset($_SESSION["ID"])) {
    header("Location: admin_login.php"); // Redirect to login if not logged in
    exit();
}

$admin_id = $_SESSION["ID"];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = $_POST["current_password"];
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];

    // Fetch the current password from the database
    $sql = "SELECT password FROM admin_user WHERE ID = '$admin_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stored_password = $row["password"]; // Plain text password from the database

        // Verify the current password (plain text comparison)
        if ($current_password === $stored_password) {
            // Check if the new password and confirm password match
            if ($new_password === $confirm_password) {
                // Update the password in the database (plain text)
                $update_sql = "UPDATE admin_user SET password = '$new_password' WHERE ID = '$admin_id'";
                if ($conn->query($update_sql)) {
                    $success_message = "Password updated successfully!";
                } else {
                    $error_message = "Error updating password: " . $conn->error;
                }
            } else {
                $error_message = "New password and confirm password do not match.";
            }
        } else {
            $error_message = "Current password is incorrect.";
        }
    } else {
        $error_message = "Admin not found.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Change Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 500px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 160px;
            margin-left: 500px;
        }

        h1 {
            text-align: center;
            color: #1a2a6c; /* Dark blue */
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #1a2a6c; /* Dark blue */
        }

        .form-group .input-icon {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-group .input-icon i {
            font-size: 20px;
            color: #1a2a6c; /* Dark blue */
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }

        .success-message {
            color: green;
            text-align: center;
            margin-bottom: 15px;
        }

        .update-button {
            width: 100%;
            margin-top: 20px;
        }

        .update-button button {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 4px;
            background-color: #1a2a6c; /* Dark blue */
            color: white;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .update-button button:hover {
            background-color: #0d1a3d; /* Darker blue */
        }

        /* Sidebar Styling */
        .sidebar {
            width: 250px;
            background: linear-gradient(to bottom, #1a2a6c, #b21f1f, #fdbb2d); /* Gradient colors */
            height: 100vh;
            padding-top: 20px;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            box-shadow: 5px 0 10px rgba(0, 0, 0, 0.2);
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
            margin-top: 20px;
        }

        .sidebar ul li {
            padding: 10px 20px;
            margin: -15px 0;
            cursor: pointer;
            font-size: 15px;
            font-weight: bold;
            border-left: -10px solid transparent;
            transition: all 0.3s ease-in-out;
        }

        .sidebar ul li a {
            color: white;
            text-decoration: none;
            font-size: 20px;
            display: block;
            padding: 12px;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: bold;
        }

        .sidebar ul li:hover {
            background: rgba(255, 255, 255, 0.1); /* Lighter hover effect */
            border-left: 4px solid #fdbb2d; /* Gold border */
        }

        /* Header - Full Width */
        .header {
            width: calc(100% - 250px);
            height: 80px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #1a2a6c; /* Dark blue */
            padding: 20px;
            color: white;
            position: fixed;
            top: 0;
            left: 250px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            font-size: 25px;
            font-weight: bold;
            margin: 0;
        }

        /* Profile Section */
        .profile {
            display: flex;
            flex-direction: column; /* Stack items vertically */
            align-items: center; /* Center the profile image and text */
            margin-left: auto; /* Slight left alignment */
            margin-right: 50px;
        }

        /* Profile Image */
        .profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid white;
            margin-bottom: 5px; /* Space between image and name */
        }

        /* Hide/Show Button Styling */
        .toggle-password {
            cursor: pointer;
            margin-left: 10px;
            color: #1a2a6c; /* Dark blue */
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <ul>
            <li><a href="admin_dashboard.php">📊 Dashboard</a></li>
            <li><a href="employee_details.php">👨‍💼 Employee</a></li>
            <li><a href="leave.php">📅 Leave</a></li>
            <li><a href="leave_report.php">📅 Leave Report</a></li>
            <li><a href="salary_report.php">💰 Salary Report</a></li>
            <li><a href="admin_change_password.php">🔒 Change Password</a></li>
            <li><a href="admin_logout.php">🏃‍♂️ Logout</a></li>
        </ul>
    </div>

    <!-- Header -->
    <div class="header">
        <h1 style="color:white;">CHANGE YOUR PASSWORD</h1>
        <div class="profile">
            <img src="../images/profile.jpg" alt="Profile">
        </div>
    </div>

    <div class="container">
        <h1>Change Password</h1>

        <!-- Display error or success messages -->
        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <!-- Change Password Form -->
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <!-- Current Password -->
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <div class="input-icon">
                    <i class="fas fa-lock"></i> <!-- Font Awesome lock icon -->
                    <input type="password" id="current_password" name="current_password" required>
                    <i class="fas fa-eye toggle-password" onclick="togglePassword('current_password')"></i> <!-- Hide/Show Button -->
                </div>
            </div>

            <!-- New Password -->
            <div class="form-group">
                <label for="new_password">New Password</label>
                <div class="input-icon">
                    <i class="fas fa-key"></i> <!-- Font Awesome key icon -->
                    <input type="password" id="new_password" name="new_password" required>
                    <i class="fas fa-eye toggle-password" onclick="togglePassword('new_password')"></i> <!-- Hide/Show Button -->
                </div>
            </div>

            <!-- Confirm New Password -->
            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <div class="input-icon">
                    <i class="fas fa-key"></i> <!-- Font Awesome key icon -->
                    <input type="password" id="confirm_password" name="confirm_password" required>
                    <i class="fas fa-eye toggle-password" onclick="togglePassword('confirm_password')"></i> <!-- Hide/Show Button -->
                </div>
            </div>

            <!-- Submit Button -->
            <div class="update-button">
                <button type="submit">Update Password</button>
            </div>
        </form>
    </div>

       <!-- JavaScript for Hide/Show Password -->
    <script>
        // Toggle password visibility
        function togglePassword(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const toggleIcon = passwordField.nextElementSibling;

            if (passwordField.type === "password") {
                passwordField.type = "text";
                toggleIcon.classList.remove("fa-eye");
                toggleIcon.classList.add("fa-eye-slash");
            } else {
                passwordField.type = "password";
                toggleIcon.classList.remove("fa-eye-slash");
                toggleIcon.classList.add("fa-eye");
            }
        }

        // Validate password before form submission
        function validatePassword() {
            const password = document.getElementById("new_password").value;
            const confirmPassword = document.getElementById("confirm_password").value;

            // Check if passwords match
            if (password !== confirmPassword) {
                alert("New password and confirm password do not match.");
                return false;
            }

            // Password requirements
            const requirements = {
                length: password.length >= 8,
                uppercase: /[A-Z]/.test(password),
                lowercase: /[a-z]/.test(password),
                number: /[0-9]/.test(password),
                special: /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password),
            };

            // Check if all requirements are met
            if (!requirements.length) {
                alert("Password must be at least 8 characters long.");
                return false;
            }
            if (!requirements.uppercase) {
                alert("Password must contain at least one uppercase letter.");
                return false;
            }
            if (!requirements.lowercase) {
                alert("Password must contain at least one lowercase letter.");
                return false;
            }
            if (!requirements.number) {
                alert("Password must contain at least one number.");
                return false;
            }
            if (!requirements.special) {
                alert("Password must contain at least one special character.");
                return false;
            }

            return true;
        }

        // Attach validation to form submission
        document.querySelector("form").addEventListener("submit", function (event) {
            if (!validatePassword()) {
                event.preventDefault(); // Prevent form submission if validation fails
            }
        });
    </script>
</body>
</html>