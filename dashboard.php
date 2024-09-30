<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AMIKAS HOSTEL | DASHBOARD</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" href="images/house.jpeg" type="image/x-icon">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            background-color: #f8f9fa;
        }

        .sidebar {
            width: 250px;
            background-color: #343a40;
            color: white;
            height: 100vh;
            padding: 20px;
            transition: transform 0.3s ease;
        }

        .close-btn {
            font-size: 24px;
            color: white;
            cursor: pointer;
            float: right;
            margin-top: -10px;
            margin-bottom: 10px;
        }

        .sidebar h2 {
            color: #ffffff;
        }

        .sidebar a {
            display: block;
            color: #ffffff;
            text-decoration: none;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .sidebar a:hover {
            background-color: #495057;
        }

        .main {
            flex: 1;
            padding: 20px;
            transition: margin-left 0.3s ease;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #f8f9fa;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            margin: 0;
        }

        .toggle-btn {
            display: none;
            cursor: pointer;
        }

        .dashboard {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-top: 20px;
        }

        .card:hover {
            transform: translateY(-20px);
        }

        .card {
            justify-content: center;
            align-items: center;
            background-color: white;
            border-radius: 5px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            margin: 30px 0;
            min-height: 150px;
            transition: transform 0.3s;
        }

        .card h3 {
            margin: 0 0 10px;
            font-size: 28px;
            text-align: center;
        }

        .card p {
            margin: 0;
            font-size: 20px;
            color: #666;
            text-align: center;
        }

        @media (max-width: 768px) {
            .dashboard {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .dashboard {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <?php
    session_start();
    // Check if user is logged in
    if (!isset($_SESSION['username'])) {
        header("Location: login.php"); // Redirect to login if not logged in
        exit;
    }

    // Include the database connection file
    include 'db.php'; // Adjust the path as necessary

    $room_type = $_SESSION['room_type'] ?? null;
    $rent = null;

    if ($room_type) {
        // Prepare a statement to get the rent based on room type
        $stmt = $conn->prepare("SELECT rent FROM rooms WHERE room_type = ?");
        $stmt->bind_param("s", $room_type);
        $stmt->execute();
        $stmt->bind_result($rent);
        $stmt->fetch();
        $stmt->close();
    }

    // If rent is not found, set it to a default value (optional)
    if ($rent === null) {
        $rent = 0; // or any default value you prefer
    }
    ?>

    <div class="sidebar" id="sidebar">
        <h2>Dashboard</h2>
        <span class="close-btn" onclick="toggleSidebar()">&times;</span>
        <a href="#">Home</a>
        <a href="#">Profile</a>
        <a href="#">Settings</a>
        <a href="#">Reports</a>
        <a href="login.php">Logout</a>
    </div>

    <div class="main">
        <div class="header">
            <span class="toggle-btn" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </span>
            <h1>Welcome: <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
            <div>
                <i class="fas fa-bell"></i>
                <i class="fas fa-user-circle"></i>
            </div>
        </div>
        <div class="dashboard">
            <div class="card">
                <h3><i class="fas fa-id-badge"></i> Tenant ID</h3>
                <p><?php echo htmlspecialchars($_SESSION['tenant_id']); ?></p>
            </div>
            <div class="card">
                <h3><i class="fas fa-door-open"></i> My Room</h3>
                <p><?php echo htmlspecialchars($_SESSION['room_number']); ?></p>
            </div>
            <div class="card">
                <h3><i class="fas fa-bed"></i> Room Type</h3>
                <p><?php echo htmlspecialchars($_SESSION['room_type']); ?></p>
            </div>
            <div class="card">
                <h3><i class="fas fa-money-bill-wave"></i> Rent Payable</h3>
                <p>Ksh. <?php echo number_format($rent, 2); ?></p>
            </div>
            <div class="card">
                <h3><i class="fas fa-tools"></i> Maintenance Issues</h3>
                <p>1</p>
            </div>
            <div class="card">
                <h3><i class="fas fa-home"></i> Available Rooms</h3>
                <p>3</p>
            </div>
            <div class="card">
                <h3><i class="fas fa-dollar-sign"></i> Pay Rent</h3>
                <p>Ksh. <?php echo number_format($rent, 2); ?></p>
            </div>
            <div class="card">
                <h3><i class="fas fa-chart-line"></i> Total Rent Paid</h3>
                <p>Ksh. 21,000</p>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
        }
    </script>
</body>

</html>
