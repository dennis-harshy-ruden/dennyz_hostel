<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AMIKAS HOSTEL | LOG IN</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" href="images/house.jpeg" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
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
            width: 94%;
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
    </style>
</head>

<body>
    <?php
    // Start session
    session_start();
    // Include the database connection file
    include 'db.php';

    // Initialize variables
    $email = $password = '';
    $email_error = $password_error = '';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get the form data
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        // Validate email
        if (empty($email)) {
            $email_error = "Email is required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_error = "Invalid email format.";
        }

        // Validate password
        if (empty($password)) {
            $password_error = "Password is required.";
        }

        // Check credentials in the database
        if (empty($email_error) && empty($password_error)) {
            // Prepare statement to check for existing records
            $stmt = $conn->prepare("SELECT username, tenant_id, room_type, room_number, password FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                $email_error = "No user found with this email.";
            } else {
                $row = $result->fetch_assoc();
                // Verify the password
                if (!password_verify($password, $row['password'])) {
                    $password_error = "Incorrect password.";
                } else {
                    // Store user data in sessions
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['tenant_id'] = $row['tenant_id'];
                    $_SESSION['room_type'] = $row['room_type'];
                    $_SESSION['room_number'] = $row['room_number'];

                    // Successful login
                    echo "<script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Login Successful!',
                            text: 'You have logged in successfully.',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'dashboard.php'; // Redirect to your dashboard
                            }
                        });
                    </script>";
                }
            }
            $stmt->close();
        }
    }

    $conn->close();
    ?>

    <form method="POST" action="">
        <h2>Login</h2>
        <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required>
        <p class="error"><?php echo $email_error; ?></p>

        <div class="password-container">
            <input type="password" name="password" id="password" placeholder="Password" required>
            <span class="toggle-password" onclick="togglePassword()">
                <i class="fas fa-eye" id="eye-icon"></i>
            </span>
        </div>
        <p class="error"><?php echo $password_error; ?></p>

        <button type="submit">Login</button>

        <footer>
            <a href="register_form.php">Create Account</a> | 
            <a href="forgot_password.php">Forgot Password?</a>
        </footer>
    </form>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            passwordField.type = passwordField.type === 'password' ? 'text' : 'password';
            eyeIcon.classList.toggle('fa-eye-slash');
        }
    </script>
</body>

</html>
