<?php
// Start the session
session_start();

// Debugging: Check if the session is working
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Check if the employee is logged in
if (!isset($_SESSION['employee_id'])) {
    header("Location: employee_login.php"); // Redirect to login if not logged in
    exit();
}

$employee_id = $_SESSION['employee_id']; // Fetch the Employee_ID from the session

// Debugging: Check if Employee_ID is set
echo "Employee ID: " . $employee_id;

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

// Handle leave request submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_leave'])) {
    $leave_type = $_POST['leave_type'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $reason = $_POST['reason'];

    // Validate dates
    if ($start_date > $end_date) {
        $error_message = "End date cannot be before start date.";
    } else {
        // Insert leave request into the database
        $sql = "INSERT INTO leave_request (Employee_ID, Leave_Type, Start_Date, End_Date, Reason, Status)
                VALUES ('$employee_id', '$leave_type', '$start_date', '$end_date', '$reason', 'Pending')";
        if ($conn->query($sql)) {
            $success_message = "Leave request submitted successfully!";
        } else {
            $error_message = "Error submitting leave request: " . $conn->error;
        }
    }
}

// Fetch the employee's leave requests
$sql = "SELECT * FROM leave_request WHERE Employee_ID = $employee_id ORDER BY Start_Date DESC";
$result = $conn->query($sql);

if (!$result) {
    die("Error fetching leave requests: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Leave</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 950px;
            margin: 50px auto;
            background: powderblue;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-left: 300px;
            margin-top: 20px;
        }

        h1 {
            text-align: center;
            color: #333;
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

        .input-icon {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon i {
            position: absolute;
            left: 10px;
            color: #1a2a6c;
            font-size: 16px;
        }

        .input-icon input, .input-icon select, .input-icon textarea {
            width: 100%;
            padding: 10px 10px 10px 35px; /* Add padding for the icon */
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        .input-icon textarea {
            resize: vertical;
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

        .submit-button {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 4px;
            background-color: #1a2a6c;
            color: white;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .submit-button:hover {
            background-color: #0d1a3d;
        }

        .leave-table {
            width: 100%;
            margin-top: 20px;
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
            z-index: 1000; /* Ensure sidebar is above other content */
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
            z-index: 1000; /* Add this line to fix the dropdown issue */
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
        <h1 style="color:white;">APPLY FOR LEAVE</h1> 
        <div class="profile">
            <img src="../images/profile.jpg" alt="Profile">
        </div>
    </div>



    <div class="container">
        <h1>Request Leave</h1>

        <!-- Display error or success messages -->
        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <!-- Leave Request Form -->
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="leave_type">Leave Type</label>
                <div class="input-icon">
                    <i class="fas fa-calendar-alt"></i> <!-- Font Awesome icon -->
                    <select id="leave_type" name="leave_type" required>
                        <option value="">Select</option>
                        <option value="Sick Leave">Sick Leave</option>
                        <option value="Casual Leave">Casual Leave</option>
                        <option value="Vacation">Vacation</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="start_date">Start Date</label>
                <div class="input-icon">
                    <i class="fas fa-calendar-day"></i> <!-- Font Awesome icon -->
                    <input type="date" id="start_date" name="start_date" required>
                </div>
            </div>

            <div class="form-group">
                <label for="end_date">End Date</label>
                <div class="input-icon">
                    <i class="fas fa-calendar-day"></i> <!-- Font Awesome icon -->
                    <input type="date" id="end_date" name="end_date" required>
                </div>
            </div>

            <div class="form-group">
                <label for="reason">Reason</label>
                <div class="input-icon">
                    <i class="fas fa-comment"></i> <!-- Font Awesome icon -->
                    <textarea id="reason" name="reason" rows="4" required></textarea>
                </div>
            </div>

            <button type="submit" name="submit_leave" class="submit-button">Submit Leave Request</button>
        </form>

        <!-- Display Leave Requests -->
        <h2>Your Leave Requests</h2>
        <table class="leave-table">
            <thead>
                <tr>
                    <th>Leave Type</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Reason</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['Leave_Type']; ?></td>
                            <td><?php echo $row['Start_Date']; ?></td>
                            <td><?php echo $row['End_Date']; ?></td>
                            <td><?php echo $row['Reason']; ?></td>
                            <td class="status <?php echo strtolower($row['Status']); ?>"><?php echo $row['Status']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">No leave requests found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php
    // Close the database connection
    $conn->close();
    ?>
</body>
</html>