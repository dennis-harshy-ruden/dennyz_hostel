<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
        }

        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .card {
            background-color: white;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card h3 {
            margin: 0 0 10px;
            font-size: 18px;
        }

        .card p {
            margin: 0;
            font-size: 14px;
            color: #666;
        }

        @media (max-width: 768px) {
            .dashboard {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }
        }
    </style>
</head>
<body>

    <div class="dashboard">
        <div class="card">
            <h3>Total Tenants</h3>
            <p>12</p>
        </div>
        <div class="card">
            <h3>Available Rooms</h3>
            <p>3</p>
        </div>
        <div class="card">
            <h3>Pending Requests</h3>
            <p>2</p>
        </div>
        <div class="card">
            <h3>Upcoming Check-ins</h3>
            <p>4</p>
        </div>
        <div class="card">
            <h3>Maintenance Issues</h3>
            <p>1</p>
        </div>
        <div class="card">
            <h3>Total Revenue</h3>
            <p>$1500</p>
        </div>
        <div class="card">
            <h3>New Registrations</h3>
            <p>1</p>
        </div>
        <div class="card">
            <h3>Messages</h3>
            <p>5</p>
        </div>
    </div>

</body>
</html>
