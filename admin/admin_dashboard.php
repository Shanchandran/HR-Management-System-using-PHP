<?php
// Start session
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "hrms";

$conn = mysqli_connect($servername, $username, $password, $database);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch employee count
$employee_query = "SELECT COUNT(*) as total FROM employees";
$employee_result = mysqli_query($conn, $employee_query);
$employee_count = mysqli_fetch_assoc($employee_result)['total'];

// Fetch leave request count
$leave_query = "SELECT COUNT(*) as total FROM leave_request";
$leave_result = mysqli_query($conn, $leave_query);
$leave_count = mysqli_fetch_assoc($leave_result)['total'];

// Admin details (Assuming session stores logged-in user details)
$admin_name = $_SESSION['admin_name'] ?? 'Admin User';
$admin_role = $_SESSION['admin_role'] ?? 'admin';
$admin_image = $_SESSION['admin_image'] ?? 'profile.jpg'; // Default profile image
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
/* General Styles */
body {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
    display: flex;
    background: #ecf0f1;
    height: 100vh;
    overflow: hidden;
}

/* Sidebar Styling */
.sidebar {
    width: 250px;
    background: linear-gradient(to bottom, #1a2a6c, #b21f1f, #fdbb2d);
    height: 100vh;
    padding-top: 20px;
    color: white;
    position: fixed;
    top: 0;
    left: 0;
    box-shadow: 5px 0 10px rgba(0, 0, 0, 0.2);
}

.sidebar .menu-icon {
    font-size: 30px;
    text-align: center;
    cursor: pointer;
    display: block;
    margin-bottom: 20px;
}

.sidebar ul {
    list-style: none;
    padding: 0;
    margin-top: 20px;
}

.sidebar ul li {
    padding: 10px 20px; /* Reduce padding */
    margin: -15px 0; /* Reduce margin between items */
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
    background: rgba(150,85,200, 0.5);
    border-left: 4px solid #fdbb2d;
}

/* Main Content */
.main-content {
    flex: 1;
    margin-left: 250px; /* Adjusted to match sidebar width */
    padding: 20px;
    background: #f4f6f9;
    height: 100vh;
    overflow-y: auto; /* Allows scrolling inside content */
}

/* Header - Full Width */
.header {
    width: calc(100% - 250px); /* Header starts from the end of the sidebar */
    height: 80px; /* Increased height */
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #1a2a6c;
    padding: 20px;
    color: white;
    border-radius: 0; /* No rounded corners */
    position: fixed;
    top: 0;
    left: 250px; /* Align with the sidebar */
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
}

/* Header Text */
.header h1 {
    font-size: 22px;
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

/* Profile Name */
.profile span {
    font-weight: bold;
    font-size: 16px;
    text-align: center; /* Center the text */
}

/* Profile Role */
.profile p {
    margin: 0;
    font-size: 14px;
    color: #dfe6e9;
    text-align: center; /* Center the text */
}

/* Adjust Main Content Below Header */
.dashboard-content {
    display: flex;
    gap: 15px;
    margin-top: 100px; /* Push content below fixed header */
    flex-wrap: wrap;
}

/* Dashboard Boxes */
.box {
    background: powderblue;
    padding: 20px;
    flex: 1;
    min-width: 200px;
    text-align: center;
    border-radius: 8px;
    box-shadow: 3px 3px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease-in-out;
    margin-top: 100px;
}

.box:hover {
    transform: translateY(-3px);
}

.box p {
    font-size: 16px;
    font-weight: bold;
    color: #555;
}

.box h2 {
    font-size: 24px;
    color: #1a2a6c;
    margin-top: 8px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .sidebar {
        width: 200px;
    }

    .header {
        width: calc(100% - 200px);
        left: 200px;
    }

    .main-content {
        margin-left: 200px;
    }

    .header h1 {
        font-size: 18px;
    }

    .profile span {
        font-size: 14px;
    }

    .box {
        min-width: 100%;
    }
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

    <!-- Main Content -->
    <div class="main-content">

        <!-- Header -->
        <div class="header">
            <h1>HRM</h1>
            <div class="profile">
                <img src="../images/profile.jpg" alt="Profile">
                <p><?php echo $admin_name; ?></p>
                
            </div>
        </div>

        <!-- Dashboard Content -->
        <div class="dashboard-content">
            <div class="box">
                <p>👥 Employee</p>
                <h2><?php echo $employee_count; ?></h2>
            </div>
            <div class="box">
                <p>📄 Leave Request</p>
                <h2><?php echo $leave_count; ?></h2>
            </div>
        </div>

    </div>

</body>
</html>