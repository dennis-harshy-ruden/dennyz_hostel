<?php
// Database configuration
$host = 'localhost'; // Change to your database host
$dbname = 'tenant'; // Change to your database name
$username = 'root'; // Change to your database username
$password = '2718@Denny'; // Change to your database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database $dbname: " . $e->getMessage());
}

$name = $email = $password = $confirm_password = $phone = $room_number = $room_type = '';
$name_error = $email_error = $password_error = $confirm_password_error = $phone_error = $room_number_error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
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
    } else {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        if ($stmt->rowCount() > 0) {
            $email_error = "Email already exists.";
        }
    }

    if (empty($phone)) {
        $phone_error = "Phone number is required.";
    } else {
        // Check if phone number already exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE phone = :phone");
        $stmt->execute(['phone' => $phone]);
        if ($stmt->rowCount() > 0) {
            $phone_error = "Phone number already exists.";
        }
    }

    if (empty($room_number)) {
        $room_number_error = "Room number is required.";
    } else {
        // Check if room number already exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE room_number = :room_number");
        $stmt->execute(['room_number' => $room_number]);
        if ($stmt->rowCount() > 0) {
            $room_number_error = "Room number already exists.";
        }
    }

    if (empty($password)) {
        $password_error = "Password is required.";
    } elseif (strlen($password) < 6) {
        $password_error = "Password must be at least 6 characters.";
    }

    if ($password !== $confirm_password) {
        $confirm_password_error = "Passwords do not match.";
    }

    // Proceed if no errors
    if (empty($name_error) && empty($email_error) && empty($phone_error) && empty($room_number_error) && empty($password_error) && empty($confirm_password_error)) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert user data into the database
        $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, room_number, room_type, password, created_at) VALUES (:name, :email, :phone, :room_number, :room_type, :password, NOW())");
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'room_number' => $room_number,
            'room_type' => $room_type,
            'password' => $hashed_password,
        ]);

        // Generate a random tenant ID after the user is inserted
        $tenantID = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));

        // Update the database with the generated tenant ID
        $stmt = $pdo->prepare("UPDATE users SET tenant_id = :tenant_id WHERE email = :email");
        $stmt->execute([
            'tenant_id' => $tenantID,
            'email' => $email,
        ]);

    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AMIKAS HOSTEL | REGISTER</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" href="images/house.jpeg" type="image/x-icon">
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
        form {
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
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
        .error {
            color: red;
            margin: 0 0 10px 0;
        }
    </style>
</head>
<body>
    <form method="" action="">
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
        </select>

        <input type="text" name="room_number" placeholder="Room Number" value="<?php echo htmlspecialchars($room_number); ?>" required>
        <p class="error"><?php echo $room_number_error; ?></p>

        <input type="password" name="password" placeholder="Password" required>
        <p class="error"><?php echo $password_error; ?></p>

        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <p class="error"><?php echo $confirm_password_error; ?></p>

        <button type="submit">Register</button>
    </form>
</body>
</html>
