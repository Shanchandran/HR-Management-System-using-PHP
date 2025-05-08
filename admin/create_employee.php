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

// Admin details (Assuming session stores logged-in user details)
$admin_name = $_SESSION['admin_name'] ?? 'Admin User';
$admin_role = $_SESSION['admin_role'] ?? 'admin';
$admin_image = $_SESSION['admin_image'] ?? 'profile.jpg'; // Default profile image

// PHP code for handling form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employeeId = $_POST['employeeId'];
    $firstName = $_POST['firstName'];
    $middleName = $_POST['middleName'];
    $lastName = $_POST['lastName'];
    $mobileNumber = $_POST['mobileNumber'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password for security
    $designation = $_POST['designation']; // Get the selected designation

    // Assign salary based on designation
    $salary = '';
    switch (strtolower($designation)) {
        case 'manager':
            $salary = '100000'; // 1L
            break;
        case 'hr':
            $salary = '80000'; // 80k
            break;
        case 'developer':
            $salary = '50000'; // 50k
            break;
        case 'designer':
            $salary = '30000'; // 30k
            break;
        case 'intern':
            $salary = '10000'; // 10k
            break;
        default:
            $salary = '0'; // If designation doesn't match
    }

    $conn = new mysqli("localhost", "root", "", "hrms");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Insert query including salary
    $sql = "INSERT INTO employees (Employee_ID, First_Name, Middle_Name, Last_Name, Mobile_Number, Email, Password, Designation, Salary)
            VALUES ('$employeeId', '$firstName', '$middleName', '$lastName', '$mobileNumber', '$email', '$password', '$designation', '$salary')";

    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully with salary ₹" . number_format($salary);
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }


// Close the database connection
$conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Employee</title>
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

        /* Container for Form */
        .container {
            width: 1000px;
            margin: 150px auto 50px auto;
            background: powderblue;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        /* Form Styling */
        form {
            display: flex;
            flex-direction: column;
        }

        .form-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .form-group {
            flex: 1;
            margin-right: 10px;
        }

        .form-group:last-child {
            margin-right: 0;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }

        .input-with-icon {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-with-icon i {
            position: absolute;
            left: 10px;
            color: #555;
        }

        .input-with-icon input, .input-with-icon select {
            width: 100%;
            padding: 10px 10px 10px 35px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        .password-toggle {
            position: absolute;
            right: 10px;
            cursor: pointer;
            color: #555;
            background: transparent;
            border: none;
            padding: 0;
        }

        .password-toggle:hover {
            color: #333;
        }

        .password-requirements {
            margin-top: 5px;
            font-size: 14px;
            color: #666;
        }

        button {
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
            font-size: 16px;
        }

        button:hover {
            background-color: #218838;
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
            <h2>HRM</h2>
            <div class="profile">
                <img src="../images/profile.jpg" alt="Profile">
                <p><?php echo $admin_name; ?></p>
            </div>
        </div>

        <!-- Container for Form -->
        <div class="container">
            <h1>Create Employee</h1>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" onsubmit="return validatePassword()">
                <!-- Row 1: Employee ID and First Name -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="employeeId">Employee ID</label>
                        <div class="input-with-icon">
                            <i class="fas fa-id-card"></i>
                            <input type="text" id="employeeId" name="employeeId" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="firstName">First Name</label>
                        <div class="input-with-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" id="firstName" name="firstName" required>
                        </div>
                    </div>
                </div>

                <!-- Row 2: Middle Name and Last Name -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="middleName">Middle Name</label>
                        <div class="input-with-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" id="middleName" name="middleName">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name</label>
                        <div class="input-with-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" id="lastName" name="lastName">
                        </div>
                    </div>
                </div>

                <!-- Row 3: Mobile Number and Designation -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="mobileNumber">Mobile Number</label>
                        <div class="input-with-icon">
                            <i class="fas fa-phone"></i>
                            <input type="tel" id="mobileNumber" name="mobileNumber" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="designation">Designation</label>
                        <div class="input-with-icon">
                            <i class="fas fa-briefcase"></i>
                            <select id="designation" name="designation" required>
                                <option value="">Select Designation</option>
                                <option value="Manager">Manager</option>
                                <option value="Developer">Developer</option>
                                <option value="Designer">Designer</option>
                                <option value="HR">HR</option>
                                <option value="Intern">Intern</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Row 4: Email and Password -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <div class="input-with-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password" name="password" required style="padding-right: 40px;">
                            <span class="password-toggle" onclick="togglePasswordVisibility()" style="right: 10px;">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                        <div class="password-requirements">
                            Password must be at least 8 characters long and include:
                            <ul>
                                <li>One uppercase letter</li>
                                <li>One lowercase letter</li>
                                <li>One number</li>
                                <li>One special character</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit">Create Employee</button>
            </form>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.querySelector('.password-toggle i');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        function validatePassword() {
            const password = document.getElementById('password').value;
            const requirements = {
                length: password.length >= 8,
                uppercase: /[A-Z]/.test(password),
                lowercase: /[a-z]/.test(password),
                number: /[0-9]/.test(password),
                specialChar: /[!@#$%^&*(),.?":{}|<>]/.test(password),
            };

            if (!requirements.length || !requirements.uppercase || !requirements.lowercase || !requirements.number || !requirements.specialChar) {
                alert('Password does not meet the requirements.');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>