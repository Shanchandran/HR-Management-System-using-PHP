<?php
// Database connection details
$servername = "localhost"; // Change if your database is hosted elsewhere
$username = "root"; // Default username for localhost
$password = ""; // Default password for localhost (empty)
$dbname = "hrms"; // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch employee details based on the Employee ID
if (isset($_GET['id'])) {
    $employeeId = $_GET['id'];
    $sql = "SELECT * FROM employees WHERE Employee_ID = $employeeId";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $employee = $result->fetch_assoc();
    } else {
        die("Employee not found.");
    }
} else {
    die("Invalid request.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
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
            background: rgba(150, 85, 200, 0.5);
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

        .header h2 {
            font-size: 22px;
            font-weight: bold;
            margin: 0;
        }

        /* Profile Section */
        .profile {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-left: auto;
            margin-right: 50px;
        }

        .profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid white;
            margin-bottom: 5px;
        }

        .profile p {
            margin: 0;
            font-size: 14px;
            color: #dfe6e9;
            text-align: center;
        }

        .container {
            width: 1000px;
            margin: 50px auto;
            background: powderblue;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: flex-start;
            margin-top: 130px;
        }

        .profile-picture {
            width: 200px;
            margin-right: 20px;
            margin-top: 50px;
        }

        .profile-picture img {
            width: 100%;
        }

        .employee-details {
            flex: 1;
        }

        .employee-details h1 {
            text-align: center;
            color: #333;
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        .form-group .input-icon {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-group .input-icon i {
            font-size: 20px;
            color: #1a2a6c;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            background-color: #f9f9f9;
            cursor: not-allowed;
        }

        /* Address Section */
        .address-section {
            margin-top: 20px;
            text-align: left; /* Align address fields to the right */
        }

        .address-section .form-group {
            margin-bottom: 15px;
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
            <h2>HRM <br> <br> Employee Profile </style></h2>
            <div class="profile">
                <img src="../images/profile.jpg" alt="Profile">
            </div>
        </div>

        <div class="container">
            <!-- Profile Picture -->
            <div class="profile-picture">
                <img src="../uploads/<?php echo $employee['Profile_Photo']; ?>" alt="Profile Picture">
            </div>

            <!-- Employee Details -->
            <div class="employee-details">
                <h1>PROFILE</h1>
                <div class="details-grid">
                    <!-- Employee ID -->
                    <div class="form-group">
                        <label for="employee_id">Employee ID</label>
                        <div class="input-icon">
                            <i class="fas fa-id-card"></i>
                            <input type="text" id="employee_id" value="<?php echo $employee['Employee_ID']; ?>" readonly>
                        </div>
                    </div>

                    <!-- First Name -->
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <div class="input-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" id="first_name" value="<?php echo $employee['First_Name']; ?>" readonly>
                        </div>
                    </div>

                    <!-- Middle Name -->
                    <div class="form-group">
                        <label for="middle_name">Middle Name</label>
                        <div class="input-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" id="middle_name" value="<?php echo $employee['Middle_Name']; ?>" readonly>
                        </div>
                    </div>

                    <!-- Last Name -->
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <div class="input-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" id="last_name" value="<?php echo $employee['Last_Name']; ?>" readonly>
                        </div>
                    </div>

                    <!-- Mobile Number -->
                    <div class="form-group">
                        <label for="mobile_number">Mobile Number</label>
                        <div class="input-icon">
                            <i class="fas fa-phone"></i>
                            <input type="text" id="mobile_number" value="<?php echo $employee['Mobile_Number']; ?>" readonly>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label for="email">Email</label>
                        <div class="input-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="text" id="email" value="<?php echo $employee['Email']; ?>" readonly>
                        </div>
                    </div>

                    <!-- Gender -->
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <div class="input-icon">
                            <i class="fas fa-venus-mars"></i>
                            <input type="text" id="gender" value="<?php echo $employee['Gender']; ?>" readonly>
                        </div>
                    </div>

                    <!-- Date of Birth -->
                    <div class="form-group">
                        <label for="dob">Date of Birth</label>
                        <div class="input-icon">
                            <i class="fas fa-birthday-cake"></i>
                            <input type="text" id="dob" value="<?php echo $employee['Date_of_Birth']; ?>" readonly>
                        </div>
                    </div>

                    <!-- Aadhaar Number -->
                    <div class="form-group">
                        <label for="aadhaar">Aadhaar Number</label>
                        <div class="input-icon">
                            <i class="fas fa-id-card"></i>
                            <input type="text" id="aadhaar" value="<?php echo $employee['Aadhaar_Number']; ?>" readonly>
                        </div>
                    </div>

                    <!-- Marital Status -->
<div class="form-group">
    <label for="marital_status">Marital Status</label>
    <div class="input-icon">
        <i class="fas fa-heart"></i>
        <input type="text" id="marital_status" value="<?php echo $employee['Marital_Status']; ?>" readonly>
    </div>
</div>

<!-- Designation -->
<div class="form-group">
    <label for="designation">Designation</label>
    <div class="input-icon">
        <i class="fas fa-briefcase"></i>
        <input type="text" id="designation" value="<?php echo $employee['Designation']; ?>" readonly>
    </div>
</div>
                </div>

                <!-- Address Section -->
                <div class="address-section">
                    <!-- Address Line 1 -->
                    <div class="form-group">
                        <label for="address1">Address Line 1</label>
                        <div class="input-icon">
                            <i class="fas fa-map-marker-alt"></i>
                            <input type="text" id="address1" value="<?php echo $employee['Address_Line_1']; ?>" readonly>
                        </div>
                    </div>

                    <!-- Address Line 2 -->
                    <div class="form-group">
                        <label for="address2">Address Line 2</label>
                        <div class="input-icon">
                            <i class="fas fa-map-marker-alt"></i>
                            <input type="text" id="address2" value="<?php echo $employee['Address_Line_2']; ?>" readonly>
                        </div>
                    </div>

                    <!-- Address Line 3 -->
                    <div class="form-group">
                        <label for="address3">Address Line 3</label>
                        <div class="input-icon">
                            <i class="fas fa-map-marker-alt"></i>
                            <input type="text" id="address3" value="<?php echo $employee['Address_Line_3']; ?>" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    // Close the database connection
    $conn->close();
    ?>
</body>
</html>