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

// Handle search functionality
$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

// Admin details (Assuming session stores logged-in user details)
$admin_name = $_SESSION['admin_name'] ?? 'Admin User';
$admin_role = $_SESSION['admin_role'] ?? 'admin';
$admin_image = $_SESSION['admin_image'] ?? 'profile.jpg'; // Default profile image

// Fetch employees from the database
$sql = "SELECT Employee_ID, First_Name, Middle_Name, Last_Name FROM employees
        WHERE Employee_ID LIKE '%$search%' OR CONCAT(First_Name, ' ', Last_Name) LIKE '%$search%'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Employees</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Add your CSS styles here */
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
            margin: 150px auto 50px auto;
            background: powderblue;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .search-bar {
            margin-bottom: 20px;
            text-align: center;
        }

        .search-bar input[type="text"] {
            width: 950px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        .search-bar button {
            padding: 10px 20px;
            background-color: purple;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .search-bar button:hover {
            background-color: blue;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: orange;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f2f2f2;
        }

        .action-button {
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .action-button:hover {
            background-color: #0056b3;
        }

        .add-staff-button {
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .add-staff-button:hover {
            background-color: gray;
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
            <h2>HRM <br> <br> Employee Details </style></h2>
            <div class="profile">
                <img src="../images/profile.jpg" alt="Profile">
                <p><?php echo $admin_name; ?></p>
            </div>
        </div>

        <!-- Container for Table -->
        <div class="container">
        <!--    <h1 style="text-align:center;">Employee List</h1>     -->
            <!-- ADD STAFF Button -->
            <button class="add-staff-button" onclick="window.location.href='create_employee.php'">ADD STAFF</button>

            <!-- Search Bar -->
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Search by Employee ID, First Name, Middle Name, or Last Name" onkeyup="searchTable()">
            </div>

            <!-- Employee Table -->
            <table id="employeeTable">
                <thead>
                    <tr>
                        <th>S. No</th>
                        <th>Employee ID</th>
                        <th>First Name</th>
                        <th>Middle Name</th>
                        <th>Last Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        $serial = 1;
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$serial}</td>
                                    <td>{$row['Employee_ID']}</td>
                                    <td>{$row['First_Name']}</td>
                                    <td>{$row['Middle_Name']}</td>
                                    <td>{$row['Last_Name']}</td>
                                    <td>
                                        <button class='action-button' onclick='viewEmployee({$row['Employee_ID']})'>VIEW</button>
                                    </td>
                                  </tr>";
                            $serial++;
                        }
                    } else {
                        echo "<tr><td colspan='6'>No employees found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Function to filter the table rows based on the search input
        function searchTable() {
            const searchInput = document.getElementById("searchInput").value.toLowerCase();
            const table = document.getElementById("employeeTable");
            const tr = table.getElementsByTagName("tr");

            for (let i = 1; i < tr.length; i++) { // Start from 1 to skip the header row
                const td = tr[i].getElementsByTagName("td");
                let match = false;
                for (let j = 0; j < td.length; j++) {
                    if (td[j].innerHTML.toLowerCase().includes(searchInput)) {
                        match = true;
                        break;
                    }
                }
                if (match) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }

        // Function to redirect to the employee details page
        function viewEmployee(employeeId) {
            window.location.href = "view_employee_details.php?id=" + employeeId;
        }
    </script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>