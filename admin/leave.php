<?php
// Start the session
session_start();

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

// Admin details (Assuming session stores logged-in user details)
$admin_name = $_SESSION['admin_name'] ?? 'Admin User';
$admin_role = $_SESSION['admin_role'] ?? 'admin';
$admin_image = $_SESSION['admin_image'] ?? 'profile.jpg'; // Default profile image

// Initialize variables
$error_message = "";
$success_message = "";

// Handle status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $employee_id = $_POST['employee_id'];
    $start_date = $_POST['start_date'];
    $status = $_POST['update_status']; // Use 'update_status' instead of 'status'

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("UPDATE leave_request SET Status = ? WHERE Employee_ID = ? AND Start_Date = ?");
    $stmt->bind_param("sis", $status, $employee_id, $start_date);

    if ($stmt->execute()) {
        $success_message = "Status updated successfully!";
    } else {
        $error_message = "Error updating status: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch all leave requests from the database
$sql = "SELECT lr.Employee_ID, e.First_Name, e.Last_Name, lr.Leave_Type, lr.Start_Date, lr.End_Date, lr.Reason, lr.Status 
        FROM leave_request lr
        JOIN employees e ON lr.Employee_ID = e.Employee_ID
        ORDER BY lr.Start_Date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Leave Management</title>
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

        /* Header - Full Width */
        .header {
            width: calc(100% - 250px);
            height: 80px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #1a2a6c;
            padding: 20px;
            color: white;
            border-radius: 0;
            position: fixed;
            top: 0;
            left: 250px;
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
            flex-direction: column;
            align-items: center;
            margin-left: auto;
            margin-right: 50px;
        }

        /* Profile Image */
        .profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid white;
            margin-bottom: 5px;
        }

        .container {
            width: 1000px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-left: 300px;
            margin-top: 150px;
        }

        h1 {
            text-align: center;
            color: #333;
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

        .leave-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .leave-table th, .leave-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .leave-table th {
            background-color: #1a2a6c;
            color: white;
        }

        .leave-table tr:hover {
            background-color: #f5f5f5;
        }

        .status-buttons {
            display: flex;
            gap: 10px;
        }

        .status-buttons button {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .status-buttons button.approved {
            background-color: #4CAF50;
            color: white;
        }

        .status-buttons button.denied {
            background-color: #f44336;
            color: white;
        }

        .status {
            font-weight: bold;
        }

        .status.pending {
            color: #ff9800;
        }

        .status.approved {
            color: #4CAF50;
        }

        .status.denied {
            color: #f44336;
        }

        .status-buttons button:disabled {
            opacity: 0.6; /* Reduce opacity for disabled buttons */
            cursor: not-allowed; /* Change cursor for disabled buttons */
        }

        .status-buttons button.approved:disabled {
            background-color: #4CAF50; /* Keep the same color but faded */
        }

        .status-buttons button.denied:disabled {
            background-color: #f44336; /* Keep the same color but faded */
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
            <h1 style="color:white;">REQUESTED LEAVE FORMS</h1>
            <div class="profile">
                <img src="../images/profile.jpg" alt="Profile">
                <p><?php echo $admin_name; ?></p>
            </div>
        </div>

        <div class="container">
            <h1>Leave Requests Management</h1>

            <!-- Display error or success messages -->
            <?php if (!empty($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <?php if (!empty($success_message)): ?>
                <div class="success-message"><?php echo $success_message; ?></div>
            <?php endif; ?>

            <!-- Leave Requests Table -->
            <table class="leave-table">
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Employee Name</th>
                        <th>Leave Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['Employee_ID']; ?></td>
                                <td><?php echo $row['First_Name'] . ' ' . $row['Last_Name']; ?></td>
                                <td><?php echo $row['Leave_Type']; ?></td>
                                <td><?php echo $row['Start_Date']; ?></td>
                                <td><?php echo $row['End_Date']; ?></td>
                                <td><?php echo $row['Reason']; ?></td>
                                <td class="status <?php echo strtolower($row['Status']); ?>"><?php echo $row['Status']; ?></td>
                                <td>
                                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                        <input type="hidden" name="employee_id" value="<?php echo $row['Employee_ID']; ?>">
                                        <input type="hidden" name="start_date" value="<?php echo $row['Start_Date']; ?>">
                                        <div class="status-buttons">
                                            <button type="submit" name="update_status" value="Approved" class="approved" <?php echo ($row['Status'] != 'Pending') ? 'disabled' : ''; ?>>Approve</button>
                                            <button type="submit" name="update_status" value="Denied" class="denied" <?php echo ($row['Status'] != 'Pending') ? 'disabled' : ''; ?>>Deny</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align: center;">No leave requests found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php
    // Close the database connection
    $conn->close();
    ?>
</body>
</html>