<?php
session_start();

$loggedInUserID = $_SESSION['employee_id'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hrms"; // Change this to your actual DB name

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$employeeData = []; // Initialize the array

if (isset($_POST['month'])) {
    $selectedMonth = $_POST['month']; // Format: YYYY-MM
    echo "Selected Month: $selectedMonth<br>"; // Debugging statement

    $monthStart = date('Y-m-01', strtotime($selectedMonth));
    $monthEnd = date('Y-m-t', strtotime($selectedMonth));
    echo "Month Start: $monthStart, Month End: $monthEnd<br>"; // Debugging statement

    $sql = "SELECT e.Employee_ID, e.First_Name, e.Designation, e.Salary,
                   l.Leave_Type, l.Start_Date, l.End_Date, l.Status
            FROM employees e
            LEFT JOIN leave_request l 
            ON e.Employee_ID = l.Employee_ID 
            AND l.Status = 'Approved' 
            AND l.Start_Date <= '$monthEnd' 
            AND l.End_Date >= '$monthStart'
            WHERE e.Employee_ID = '$loggedInUserID'
            ORDER BY e.Employee_ID";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "Data fetched successfully!<br>"; // Debugging statement
        while ($row = $result->fetch_assoc()) {
            $empId = $row['Employee_ID'];
            $empName = $row['First_Name'];
            $designation = $row['Designation'];
            $salary = $row['Salary'];
            $leaveType = $row['Leave_Type'];
            $startDate = $row['Start_Date'];
            $endDate = $row['End_Date'];

            if (!isset($employeeData[$empId])) {
                $employeeData[$empId] = [
                    'Employee_ID' => $empId,
                    'Employee_Name' => $empName,
                    'Designation' => $designation,
                    'Salary' => $salary,
                    'LOP_Days' => 0
                ];
            }

            if ($leaveType) {
                $start = new DateTime($startDate);
                $end = new DateTime($endDate);
                $totalLeaveDays = $start->diff($end)->days + 1;

                $leaveDaysInMonth = 0;
                $current = clone $start;
                for ($i = 0; $i < $totalLeaveDays; $i++) {
                    $dateStr = $current->format('Y-m-d');
                    $dayOfWeek = $current->format('N'); // 1 (Monday) to 7 (Sunday)
                    
                    // Exclude Sundays (dayOfWeek = 7)
                    if ($dateStr >= $monthStart && $dateStr <= $monthEnd && $dayOfWeek != 7) {
                        $leaveDaysInMonth++;
                    }
                    $current->modify('+1 day');
                }

                if ($leaveType == 'CL') {
                    if ($totalLeaveDays > 1) {
                        $employeeData[$empId]['LOP_Days'] += $leaveDaysInMonth;
                    }
                    // 1-day CL — no deduction
                } else {
                    $employeeData[$empId]['LOP_Days'] += $leaveDaysInMonth;
                }
            }
        }
    } else {
        echo "No data found for the selected month.<br>"; // Debugging statement
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary Report</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
    /* General Body Styling */
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }

    /* Container for Salary Report */
    .container {
        width: 950px;
        margin: 50px auto;
        background: powderblue;
        padding: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        margin-left: 300px;
        margin-top: 150px; /* Adjusted to account for the fixed header */
    }

    /* Heading Styling */
    h1 {
        text-align: center;
        color: #333;
    }

    h2 {
        text-align: center;
        color: #333;
        margin-bottom: 20px;
    }

    /* Month Picker Styling */
    .month-picker {
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .month-picker label {
        font-size: 16px;
        font-weight: bold;
        color: #333;
    }

    .month-picker input[type="month"] {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 16px;
        width: 200px;
        background-color: #f9f9f9;
        cursor: pointer;
    }

    .month-picker button {
        padding: 10px 20px;
        background-color: #1a2a6c;
        color: white;
        border: none;
        border-radius: 4px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .month-picker button:hover {
        background-color: #0d1a3d;
    }

    /* Salary Report Table Styling */
    .salary-table {
        width: 100%;
        margin-top: 20px;
        border-collapse: collapse;
        background: #fff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .salary-table th, .salary-table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    .salary-table th {
        background-color: #1a2a6c;
        color: white;
    }

    .salary-table tr:hover {
        background-color: #f5f5f5;
    }

    .salary-table td {
        color: #333;
    }

    /* No Data Message */
    .no-data {
        text-align: center;
        font-size: 16px;
        color: #666;
        margin-top: 20px;
    }

    /* Success and Error Messages */
    .success-message {
        color: green;
        text-align: center;
        margin-bottom: 15px;
    }

    .error-message {
        color: red;
        text-align: center;
        margin-bottom: 15px;
    }

    /* Sidebar Styling (Unchanged) */
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

    /* Header Styling (Unchanged) */
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

    /* Profile Section (Unchanged) */
    .profile {
        display: flex;
        flex-direction: column; /* Stack items vertically */
        align-items: center; /* Center the profile image and text */
        margin-left: auto; /* Slight left alignment */
        margin-right: 50px;
    }

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

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <h2 style="text-align:left; color:white;">HRM <br> <br> SALARY REPORT </style></h2>
            <div class="profile">
                <img src="../images/profile.jpg" alt="Profile">
            </div>
        </div>

        <!-- Container -->
        <div class="container">
            <h2 style="text-align:center; color:black;">MY SALARY REPORT</h2>
            <!-- Month Picker -->
            <div class="month-picker">
                <form method="POST" action="">
                    <label for="month">Select Month:</label>
                    <input type="month" id="month" name="month" value="<?php echo isset($selectedMonth) ? $selectedMonth : ''; ?>" required>
                    <button type="submit">Generate Report</button>
                </form>
            </div>

            <!-- Salary Report Table -->
            <?php if (!empty($employeeData)): ?>
                <table class="salary-table">
                    <thead>
                        <tr>
                            <th>Employee ID</th>
                            <th>Name</th>
                            <th>Designation</th>
                            <th>Gross Salary</th>
                            <th>LOP Days</th>
                            <th>Deduction</th>
                            <th>Net Salary</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($employeeData as $emp): ?>
                            <tr>
                                <td><?php echo $emp['Employee_ID']; ?></td>
                                <td><?php echo $emp['Employee_Name']; ?></td>
                                <td><?php echo $emp['Designation']; ?></td>
                                <td><?php echo number_format($emp['Salary'], 2); ?></td>
                                <td><?php echo $emp['LOP_Days']; ?></td>
                                <td><?php echo number_format($emp['LOP_Days'] * ($emp['Salary'] / 26), 2); ?></td>
                                <td><?php echo number_format($emp['Salary'] - ($emp['LOP_Days'] * ($emp['Salary'] / 26)), 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-data">No salary data found for the selected month.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>