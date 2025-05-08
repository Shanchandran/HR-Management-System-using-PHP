<?php
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
    $monthStart = date('Y-m-01', strtotime($selectedMonth));
    $monthEnd = date('Y-m-t', strtotime($selectedMonth));

    $sql = "SELECT e.Employee_ID, e.First_Name, e.Designation, e.Salary,
                   l.Leave_Type, l.Start_Date, l.End_Date, l.Status
            FROM employees e
            LEFT JOIN leave_request l 
            ON e.Employee_ID = l.Employee_ID 
            AND l.Status = 'Approved' 
            AND l.Start_Date <= '$monthEnd' 
            AND l.End_Date >= '$monthStart'
            ORDER BY e.Employee_ID";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
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

        /* Header Styling */
        .header {
            width: calc(100% - 250px);
            height: 80px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #1a2a6c;
            padding: 20px;
            color: white;
            position: fixed;
            top: 0;
            left: 250px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }

        .header h2 {
            font-size: 22px;
            font-weight: bold;
            margin: 0;
        }

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
        }

        /* Main Content Styling */
        .main-content {
            margin-left: 250px;
            padding-top: 100px;
            padding-left: 20px;
            padding-right: 20px;
        }

        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

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
            <h2 style="text-align:left;">HRM <br> <br> SALARY REPORT </style></h2>
            <div class="profile">
                <img src="../images/profile.jpg" alt="Profile">
                <p><?php echo "Admin User"; ?></p>
            </div>
        </div>

        <!-- Container -->
        <div class="container">
            <h2 style="text-align:center; color:black;">SALARY REPORT</h2>
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
                <table>
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
                        <?php
                        $workingDaysInMonth = 26; // Adjust this based on the actual number of working days
                        foreach ($employeeData as $emp):
                            $salaryPerDay = $emp['Salary'] / $workingDaysInMonth;
                            $deduction = $emp['LOP_Days'] * $salaryPerDay;
                            $netSalary = $emp['Salary'] - $deduction;
                        ?>
                            <tr>
                                <td><?php echo $emp['Employee_ID']; ?></td>
                                <td><?php echo $emp['Employee_Name']; ?></td>
                                <td><?php echo $emp['Designation']; ?></td>
                                <td><?php echo number_format($emp['Salary'], 2); ?></td>
                                <td><?php echo $emp['LOP_Days']; ?></td>
                                <td><?php echo number_format($deduction, 2); ?></td>
                                <td><?php echo number_format($netSalary, 2); ?></td>
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