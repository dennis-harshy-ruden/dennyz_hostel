<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AMIKAS HOSTEL | REGISTER</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" href="images/house.jpeg" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* CSS styles */
        body {
            font-family: Arial, sans-serif;
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            flex-direction: column;
        }

        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('images/house.jpeg');
            background-size: cover;
            background-position: center;
            filter: blur(8px);
            z-index: -1;
        }

        form {
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            position: relative;
        }

        input {
            width: 94.5%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .password-container {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #343a40;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #218838;
        }

        footer {
            margin-top: 20px;
            text-align: center;
        }

        a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
            color: #0056b3;
        }

        .error {
            color: red;
            margin: 0 0 10px 0;
        }

        #loader {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .spinner {
            border: 8px solid #f3f3f3;
            border-top: 8px solid #3498db;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <div id="loader">
        <div class="spinner"></div>
    </div>

    <?php
    // Include the database connection file
    include 'db.php';

    // Initialize variables to hold form data and error messages
    $name = $email = $password = $confirm_password = $phone = $room_number = $room_type = '';
    $name_error = $email_error = $password_error = $confirm_password_error = $phone_error = $room_number_error = '';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get the form data
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $phone = trim($_POST['phone']);
        $room_number = trim($_POST['room_number']);
        $room_type = $_POST['room_type'];

        // Validate inputs
        if (empty($name)) {
            $name_error = "Name is required.";
        }

        if (empty($email)) {
            $email_error = "Email is required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_error = "Invalid email format.";
        }

        if (empty($password)) {
            $password_error = "Password is required.";
        } elseif (strlen($password) < 6) {
            $password_error = "Password must be at least 6 characters.";
        }

        if (empty($confirm_password)) {
            $confirm_password_error = "Confirm password is required.";
        } elseif ($password !== $confirm_password) {
            $confirm_password_error = "Passwords do not match.";
        }

        if (empty($phone)) {
            $phone_error = "Phone number is required.";
        }

        if (empty($room_number)) {
            $room_number_error = "Room number is required.";
        }

        // Generate a tenant ID
        $tenant_id = sprintf("%06d", rand(0, 999999)); // Create a 6-digit tenant ID

        // Create the username using only the first name
        $first_name = explode(' ', $name)[0]; // Get the first word as the first name
        $username = strtolower(str_replace(' ', '', $first_name)) . $room_number;

        // Check if the email, phone, or room number already exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR phone = ? OR room_number = ?");
        $stmt->bind_param("sss", $email, $phone, $room_number);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Set error messages if email, phone, or room number exists
            while ($row = $result->fetch_assoc()) {
                if ($row['email'] === $email) {
                    $email_error = "Email already exists.";
                }
                if ($row['phone'] === $phone) {
                    $phone_error = "Phone number already exists.";
                }
                if ($row['room_number'] === $room_number) {
                    $room_number_error = "Room number already exists.";
                }
            }
        } else {
            // Only proceed if there are no errors
            if (empty($name_error) && empty($email_error) && empty($phone_error) && empty($room_number_error) && empty($password_error) && empty($confirm_password_error)) {
                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Prepare the SQL statement for registration
                $stmt = $conn->prepare("INSERT INTO users (name, email, phone, room_number, room_type, tenant_id, username, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssississ", $name, $email, $phone, $room_number, $room_type, $tenant_id, $username, $hashed_password);

                // Execute the statement
                if ($stmt->execute()) {
                    // Registration successful
                    echo "<script>
                        Swal.fire({
                            title: 'Success',
                            text: 'Your registration was successful!',
                            icon: 'success',
                            willClose: () => {
                                window.location.href = 'login.php'; // Redirect to login page
                            }
                        });
                    </script>";
                } else {
                    echo "<script>
                        Swal.fire('Error', 'Registration failed. Please try again.', 'error');
                    </script>";
                }
                
            }
        }
        $stmt->close();
        $conn->close();
    }
    ?>

    <form method="POST" action="">
        <h2>Register</h2>
        <input type="text" name="name" placeholder="Full Name" value="<?php echo htmlspecialchars($name); ?>" required>
        <p class="error"><?php echo $name_error; ?></p>

        <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required>
        <p class="error"><?php echo $email_error; ?></p>

        <input type="text" name="phone" placeholder="Phone Number" value="<?php echo htmlspecialchars($phone); ?>" required>
        <p class="error"><?php echo $phone_error; ?></p>

        <select name="room_type" required>
            <option value="">Select Room Type</option>
            <option value="bedsitter" <?php if ($room_type == 'bedsitter') echo 'selected'; ?>>Bedsitter</option>
            <option value="single" <?php if ($room_type == 'single') echo 'selected'; ?>>Single</option>
            <option value="1-bedroom" <?php if ($room_type == '1-bedroom') echo 'selected'; ?>>1 Bedroom</option>
            <option value="2-bedroom" <?php if ($room_type == '2-bedroom') echo 'selected'; ?>>2 Bedroom</option>
        </select>

        <input type="text" name="room_number" placeholder="Room Number" value="<?php echo htmlspecialchars($room_number); ?>" required>
        <p class="error"><?php echo $room_number_error; ?></p>

        <div class="password-container">
            <input type="password" name="password" id="password" placeholder="Password" required>
            <span class="toggle-password" onclick="togglePassword()">
                <i class="fas fa-eye" id="eye-icon"></i>
            </span>
        </div>
        <p class="error"><?php echo $password_error; ?></p>

        <div class="password-container">
            <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
            <span class="toggle-password" onclick="toggleConfirmPassword()">
                <i class="fas fa-eye" id="eye-icon-confirm"></i>
            </span>
        </div>
        <p class="error"><?php echo $confirm_password_error; ?></p>

        <button type="submit">Register</button>

        <footer>
            <a href="login.php">Already have an account? Login</a>
        </footer>
    </form>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            passwordField.type = passwordField.type === 'password' ? 'text' : 'password';
            eyeIcon.classList.toggle('fa-eye-slash');
        }

        function toggleConfirmPassword() {
            const confirmPasswordField = document.getElementById('confirm_password');
            const eyeIcon = document.getElementById('eye-icon-confirm');
            confirmPasswordField.type = confirmPasswordField.type === 'password' ? 'text' : 'password';
            eyeIcon.classList.toggle('fa-eye-slash');
        }
    </script>
</body>

</html>
