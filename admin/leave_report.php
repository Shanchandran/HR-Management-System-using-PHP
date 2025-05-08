<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "hrms");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize selected date
$selectedDate = "";

// Fetch leave requests for the selected date
$leaveData = [];
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['date'])) {
    $selectedDate = $_POST['date'];

    // Query to fetch approved leave requests for the selected date
    $sql = "SELECT e.Employee_ID, e.First_Name, e.Last_Name, l.Leave_Type, l.Reason, l.Start_Date, l.End_Date
            FROM leave_request l 
            JOIN employees e ON l.Employee_ID = e.Employee_ID
            WHERE l.Status = 'Approved' AND '$selectedDate' BETWEEN l.Start_Date AND l.End_Date";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $leaveData[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Report</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Use existing styles from your previous code */
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

        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 150px;
        }

        /* Date Picker Styling */
        .date-picker {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .date-picker label {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }

        .date-picker input[type="date"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            width: 200px;
            background-color: #f9f9f9;
            cursor: pointer;
        }

        .date-picker input[type="date"]:focus {
            border-color: #1a2a6c;
            outline: none;
        }

        .date-picker button {
            padding: 10px 20px;
            background-color: #1a2a6c;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .date-picker button:hover {
            background-color: #15224b;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        table th, table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #1a2a6c;
            color: white;
            font-weight: bold;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        /* No Data Message */
        .no-data {
            text-align: center;
            font-size: 16px;
            color: #666;
            margin-top: 20px;
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
            <h2 style="text-align:left;">HRM <br> <br> LEAVE REPORT </style></h2>
            <div class="profile">
                <img src="../images/profile.jpg" alt="Profile">
                <p><?php echo "Admin User"; ?></p>
            </div>
        </div>

        <!-- Container -->
        <div class="container">

        <h2 style="text-align:center, color:black;">LEAVE REPORT</h2>
            <!-- Date Picker -->
<div class="date-picker">
    <form method="POST" action="">
        <label for="date">Select Date:</label>
        <input type="date" id="date" name="date" value="<?php echo $selectedDate; ?>" required>
        <button type="submit">Generate Report</button>
    </form>
</div>

            <!-- Leave Report Table -->
            <?php if (!empty($leaveData)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Employee ID</th>
                            <th>Name</th>
                            <th>Leave Type</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($leaveData as $row): ?>
                            <tr>
                                <td><?php echo $row['Employee_ID']; ?></td>
                                <td><?php echo $row['First_Name'] . ' ' . $row['Last_Name']; ?></td>
                                <td><?php echo $row['Leave_Type']; ?></td>
                                <td><?php echo $row['Start_Date']; ?></td>
                                <td><?php echo $row['End_Date']; ?></td>
                                <td><?php echo $row['Reason']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-data">No leave requests found for the selected date.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>