<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['employee_id'])) {
    header("Location: employee_login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "hrms");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the logged-in employee's ID from session
$employee_id = $_SESSION['employee_id'];

// Fetch employee data for the logged-in user
$sql = "SELECT Employee_ID, First_Name, Middle_Name, Last_Name, Gender, Date_of_Birth, Marital_Status, 
               Address_Line_1, Address_Line_2, Address_Line_3, Profile_Photo, Aadhaar_Number, Designation 
        FROM employees 
        WHERE Employee_ID = '$employee_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $employee = $result->fetch_assoc();
    $employee_id = $employee["Employee_ID"];
    $first_name = $employee["First_Name"];
    $middle_name = $employee["Middle_Name"];
    $last_name = $employee["Last_Name"];
    $gender = $employee["Gender"];
    $dob = $employee["Date_of_Birth"];
    $marital_status = $employee["Marital_Status"];
    $address_line_1 = $employee["Address_Line_1"];
    $address_line_2 = $employee["Address_Line_2"];
    $address_line_3 = $employee["Address_Line_3"];
    $profile_photo = $employee["Profile_Photo"];
    $aadhaar_number = $employee["Aadhaar_Number"];
    $designation = $employee["Designation"] ?? "N/A";
} else {
    echo "<script>alert('Employee data not found!'); window.location.href = 'employee_dashboard.php';</script>";
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST["first_name"];
    $middle_name = $_POST["middle_name"];
    $last_name = $_POST["last_name"];
    $gender = $_POST["gender"];
    $dob = date('Y-m-d', strtotime(str_replace('-', '/', $_POST["dob"])));
    $marital_status = $_POST["marital_status"];
    $address_line_1 = $_POST["address_line_1"];
    $address_line_2 = $_POST["address_line_2"];
    $address_line_3 = $_POST["address_line_3"];
    $aadhaar_number = $_POST["aadhaar_number"];

    // Handle file upload only if a new file was selected
    if (isset($_FILES["profile_photo"]) && $_FILES["profile_photo"]["error"] == 0) {
        $target_dir = "../uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        $target_file = $target_dir . basename($_FILES["profile_photo"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if the file is an image
        $check = getimagesize($_FILES["profile_photo"]["tmp_name"]);
        if ($check !== false) {
            // Move the uploaded file to the target directory
            if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $target_file)) {
                $profile_photo = $target_file;
            } else {
                echo "<script>alert('Error moving uploaded file.');</script>";
            }
        } else {
            echo "<script>alert('File is not an image.');</script>";
        }
    }

    // Update employee details in the database
    $sql = "UPDATE employees SET 
            First_Name = '$first_name', 
            Middle_Name = '$middle_name', 
            Last_Name = '$last_name', 
            Gender = '$gender', 
            Date_of_Birth = '$dob', 
            Marital_Status = '$marital_status', 
            Address_Line_1 = '$address_line_1', 
            Address_Line_2 = '$address_line_2', 
            Address_Line_3 = '$address_line_3', 
            Aadhaar_Number = '$aadhaar_number'";
    
    // Only update profile photo if a new one was uploaded
    if (isset($target_file)) {
        $sql .= ", Profile_Photo = '$profile_photo'";
    }
    
    $sql .= " WHERE Employee_ID = '$employee_id'";

    if ($conn->query($sql)) {
        echo "<script>alert('Profile updated successfully!'); window.location.href='edit_profile.php';</script>";
    } else {
        echo "<script>alert('Error updating profile: " . $conn->error . "');</script>";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background: lightblue;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-left: 320px;
            margin-top: 120px;
        }

        h1 {
            text-align: center;
            color: #2c7a7b; /* Teal */
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group .input-icon {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-group .input-icon i {
            font-size: 20px;
            color: #2c7a7b; /* Teal */
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        .form-group input[disabled] {
            background-color: #f9f9f9;
        }

        .form-row {
            display: flex;
            gap: 20px;
        }

        .form-row .form-group {
            flex: 1;
        }

        .profile-photo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .profile-photo img {
            width: 100px;
            height: 100px;
            border-radius: 10px;
            object-fit: cover;
        }

        .buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            margin-left: 750px;
        }

        .buttons button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            background-color: #2c7a7b; /* Teal */
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        .buttons button:hover {
            background-color: #2575fc; /* Blue */
        }

        /* Update Button Styling */
        .update-button {
            width: 100%;
            margin-top: 20px;
        }

        .update-button button {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 4px;
            background-color: #4CAF50; /* Green */
            color: white;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .update-button button:hover {
            background-color: #45a049; /* Darker Green */
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
    <div class="header">
            <h2>HRM <br> <br> Edit Your Profile </style></h2>
            <div class="profile">
                <img src="../images/profile.jpg" alt="Profile">
            </div>
        </div>
    </div>

    <div class="container">
        <h1>Edit Profile</h1>

        <!-- Edit Profile Button -->
        <div class="buttons">
            <button type="button" id="edit-profile-btn">Edit Profile</button>
        </div>

        <!-- Form -->
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
            <!-- Employee ID -->
            <div class="form-group">
                <label for="employee_id">Employee ID</label>
                <div class="input-icon">
                    <i class="fas fa-id-card"></i>
                    <input type="text" id="employee_id" name="employee_id" value="<?php echo $employee_id; ?>" readonly>
                </div>
            </div>

            <!-- First Name, Middle Name, Last Name -->
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <div class="input-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" id="first_name" name="first_name" value="<?php echo $first_name; ?>" disabled required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="middle_name">Middle Name</label>
                    <div class="input-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" id="middle_name" name="middle_name" value="<?php echo $middle_name; ?>" disabled>
                    </div>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <div class="input-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" id="last_name" name="last_name" value="<?php echo $last_name; ?>" disabled required>
                    </div>
                </div>
            </div>

            <!-- Profile Photo -->
            <div class="form-group profile-photo">
                <label for="profile_photo">Profile Photo</label>
                <div class="input-icon">
                    <i class="fas fa-camera"></i>
                    <input type="file" id="profile_photo" name="profile_photo" accept="image/*" disabled>
                </div>
                
            </div>

            <!-- Designation -->
            <div class="form-group">
                <label for="designation">Designation</label>
                <div class="input-icon">
                    <i class="fas fa-briefcase"></i>
                    <input type="text" id="designation" name="designation" value="<?php echo $designation; ?>" readonly>
                </div>
            </div>

            <!-- Gender, DOB, Marital Status -->
            <div class="form-row">
                <div class="form-group">
                    <label for="gender">Gender</label>
                    <div class="input-icon">
                        <i class="fas fa-venus-mars"></i>
                        <select id="gender" name="gender" disabled required>
                            <option value="Male" <?php echo ($gender == 'Male') ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo ($gender == 'Female') ? 'selected' : ''; ?>>Female</option>
                            <option value="Other" <?php echo ($gender == 'Other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="dob">Date of Birth</label>
                    <div class="input-icon">
                        <i class="fas fa-calendar-alt"></i>
                        <input type="date" id="dob" name="dob" required value="<?php echo $dob; ?>" disabled>
                    </div>
                </div>
                <div class="form-group">
                    <label for="marital_status">Marital Status</label>
                    <div class="input-icon">
                        <i class="fas fa-heart"></i>
                        <select id="marital_status" name="marital_status" disabled required>
                            <option value="Single" <?php echo ($marital_status == 'Single') ? 'selected' : ''; ?>>Single</option>
                            <option value="Married" <?php echo ($marital_status == 'Married') ? 'selected' : ''; ?>>Married</option>
                            <option value="Divorced" <?php echo ($marital_status == 'Divorced') ? 'selected' : ''; ?>>Divorced</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Aadhaar Number -->
            <div class="form-group">
                <label for="aadhaar_number">Aadhaar Number</label>
                <div class="input-icon">
                    <i class="fas fa-id-badge"></i>
                    <input type="text" id="aadhaar_number" name="aadhaar_number" value="<?php echo $aadhaar_number; ?>" maxlength="12" pattern="\d{12}" disabled required>
                </div>
            </div>

            <!-- Address Lines -->
            <div class="form-group">
                <label for="address_line_1">Address Line 1</label>
                <div class="input-icon">
                    <i class="fas fa-map-marker-alt"></i>
                    <input type="text" id="address_line_1" name="address_line_1" value="<?php echo $address_line_1; ?>" disabled required>
                </div>
            </div>
            <div class="form-group">
                <label for="address_line_2">Address Line 2</label>
                <div class="input-icon">
                    <i class="fas fa-map-marker-alt"></i>
                    <input type="text" id="address_line_2" name="address_line_2" value="<?php echo $address_line_2; ?>" disabled>
                </div>
            </div>
            <div class="form-group">
                <label for="address_line_3">Address Line 3</label>
                <div class="input-icon">
                    <i class="fas fa-map-marker-alt"></i>
                    <input type="text" id="address_line_3" name="address_line_3" value="<?php echo $address_line_3; ?>" disabled>
                </div>
            </div>

            <!-- Buttons -->
            <div class="update-button">
                <button type="submit" id="update-profile-btn" disabled>Update Profile</button>
            </div>
        </form>
    </div>

    <!-- Bootstrap Datepicker JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

    <script>
        // Enable form fields when "Edit Profile" button is clicked
        document.getElementById('edit-profile-btn').addEventListener('click', function () {
            // Enable all disabled fields except Employee ID and Designation
            const fields = document.querySelectorAll('input:not([readonly]), select');
            fields.forEach(field => field.disabled = false);
            document.getElementById('update-profile-btn').disabled = false;
            
            // Disable the edit button after clicking
            this.disabled = true;
        });
    </script>
</body>
</html>