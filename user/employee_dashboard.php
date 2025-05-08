<?php
// Start the session
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "hrms");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch employee data for the logged-in employee
$employee_id = $_SESSION['employee_id']; // Get the logged-in employee's ID from the session
$sql = "SELECT Employee_ID, First_Name, Last_Name, Profile_Photo FROM employees WHERE Employee_ID = '$employee_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $employee = $result->fetch_assoc();
    $name = $employee["First_Name"] . " " . $employee["Last_Name"]; // Full name
    $employee_id = $employee["Employee_ID"];
    $profile_photo = $employee["Profile_Photo"]; // Path to the profile picture
} else {
    // If no employee is found, default values
    $name = "Employee Not Found";
    $employee_id = "N/A";
    $profile_photo = "default_avatar.jpg"; // Default placeholder image
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 250px;
            background: linear-gradient(to bottom, #283c86, #45a247); /* New gradient */
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
            border-left: 4px solid #ffd700; /* Gold border */
        }

        /* Header - Full Width */
        .header {
            width: calc(100% - 250px);
            height: 80px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #2c7a7b; /* New header color */
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
        .profile-section {
            width: calc(100% - 250px);
            display: flex;
            align-items: flex-start;
            margin: 20px;
            padding: 20px;
            background-color: #f4e1e1;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 150px;
            margin-left: 250px;
        }

        /* Profile Picture */
        .profile-picture {
            width: 200px;
            height: 250px;
            border-radius: 10px;
            object-fit: cover;
            margin-right: 100px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        /* Cards for Employee Details */
        .details-cards {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .card {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        .card h3 {
            margin: 0 0 10px;
            font-size: 18px;
            color: #333;
        }

        .card p {
            margin: 0;
            font-size: 16px;
            color: blue;
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

    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <ul>
            <li><a href="employee_dashboard.php">📊 Dashboard</a></li>
            <li><a href="edit_profile.php">👨‍💼 Edit Profile</a></li>
            <li><a href="leave_request.php">📅 Leave</a></li>
            <li><a href="salary_report.php">💰 Salary Report</a></li>
            <li><a href="employee_change_password.php">🔒 Change Password</a></li>
            <li><a href="employee_logout.php">🏃‍♂️ Logout</a></li>
        </ul>
    </div>

    <!-- Header -->
    <div class="header">
        <h1>Welcome, <?php echo htmlspecialchars($name); ?></h1>
        <div class="profile">
            <img src="../images/profile.jpg" alt="Profile">
        </div>
    </div>

    <!-- Profile Section -->
    <div class="profile-section">
        <!-- Profile Picture -->
        <img src="/hrms/uploads/<?php echo htmlspecialchars($profile_photo); ?>" alt="Profile Picture" class="profile-picture">

        <!-- Employee Details Cards -->
        <div class="details-cards">
            <!-- Employee Name Card -->
            <div class="card">
                <h3>Employee Name</h3>
                <p><b><i><?php echo htmlspecialchars($name); ?></b></i></p>
            </div>

            <!-- Employee ID Card -->
            <div class="card">
                <h3>Employee ID</h3>
                <p><b><i><?php echo htmlspecialchars($employee_id); ?></b></i></p>
            </div>
        </div>
    </div>
</body>
</html>