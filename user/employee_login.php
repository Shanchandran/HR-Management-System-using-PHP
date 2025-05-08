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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];


    // Fetch employee details from the database
    $sql = "SELECT * FROM employees WHERE Email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Verify the password
        if (password_verify($password, $row['Password'])) {
            // Password is correct, start a session
            session_start();
            $_SESSION['employee_id'] = $row['Employee_ID'];
            $_SESSION['employee_name'] = $row['First_Name'] . ' ' . $row['Last_Name'];
            $_SESSION['employee_email'] = $row['Email'];

            // Redirect to the employee dashboard
            header("Location: employee_dashboard.php");
            exit();
        } else {
            // Password is incorrect
            $error = "Invalid email or password.";
        }
    } else {
        // Email not found
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Login</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Reuse the same styles as the HRM login page */
        body {
    font-family: Arial, sans-serif;
    background: url('../images/employee.jpg') no-repeat center center fixed;
    background-size: cover;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}


        .container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #333;
        }

        h2 {
            font-size: 20px;
            margin-bottom: 20px;
            color: #555;
        }

        .input-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .input-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        .input-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        .login-btn {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        .login-btn:hover {
            background-color: #218838;
        }

        footer {
            margin-top: 20px;
            font-size: 12px;
            color: #777;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .error {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Human Resource Management System</h1>
        <h2>Employee Login</h2>

        <!-- Display error message if login fails -->
        <?php if (isset($error)) { ?>
            <div class="error"><?php echo $error; ?></div>
        <?php } ?>

        <form action="employee_login.php" method="POST">
            <div class="input-group">
                <label>Email:</label>
                <input type="email" name="email" placeholder="Enter Email Address" required>
            </div>
            <div class="input-group">
                <label>Password:</label>
                <input type="password" name="password" placeholder="Enter Password" required>
            </div>
            <button type="submit" class="login-btn">Sign In</button> <br>
            <a href="../admin/login.html">LOGIN AS HR</a>
        </form>

        <footer>
            <p>Human Resource Management System. All Rights Reserved © 2023</p>
        </footer>
    </div>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>