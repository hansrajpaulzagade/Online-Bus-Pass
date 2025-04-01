<?php
include 'db_connection.php';
session_start();

// Simple protection - check if logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_dashboard.php');
    exit();
}


// Query to count total users
$totalUsersQuery = "SELECT COUNT(*) AS total FROM users";
$totalUsersResult = $conn->query($totalUsersQuery);
$totalUsers = $totalUsersResult->fetch_assoc()['total'];

// Query to count revenue (sum of payments)
$revenueQuery = "SELECT SUM(cost) AS total FROM payments";
$revenueResult = $conn->query($revenueQuery);
$revenue = $revenueResult->fetch_assoc()['total'];

// Query to count messages
$messagesQuery = "SELECT COUNT(*) AS total FROM contacts";
$messagesResult = $conn->query($messagesQuery);
$totalMessages = $messagesResult->fetch_assoc()['total'];

// Query to count rejected applications
$rejectedQuery = "SELECT COUNT(*) AS total FROM students WHERE status = 'rejected'";
$rejectedResult = $conn->query($rejectedQuery);
$rejectedApplications = $rejectedResult->fetch_assoc()['total'];

// Query to count active passes
$activePassesQuery = "SELECT COUNT(*) AS total FROM payments WHERE payment_status = 'success'";
$activePassesResult = $conn->query($activePassesQuery);
$activePasses = $activePassesResult->fetch_assoc()['total'];

// Query to count active passes
$activePassesQuery = "SELECT COUNT(*) AS total FROM contacts";
$activePassesResult = $conn->query($activePassesQuery);
$activePasses = $activePassesResult->fetch_assoc()['total'];

// Query to count pending requests
$pendingRequestsQuery = "SELECT COUNT(*) AS total FROM students WHERE status = 'pending'";
$pendingRequestsResult = $conn->query($pendingRequestsQuery);
$pendingRequests = $pendingRequestsResult->fetch_assoc()['total'];
 ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>
    <style>
        body {
            display: flex;
            min-height: 100vh;
            background-color: white;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            background: linear-gradient(to right, orange, red);
            padding: 20px;
            position: fixed;
            color: white;
        }
        .sidebar a {
            color: white;
            display: block;
            padding: 10px;
            text-decoration: none;
            transition: background 0.3s;
        }
        .sidebar a:hover {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
        }
        .content {
            margin-left: 250px;
            padding: 20px;
            width: 100%;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .logout-btn {
            background: black;
            color: white;
            transition: background 0.3s ease-in-out;
        }
        .logout-btn:hover {
            background: green;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="#dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="#users"><i class="fas fa-users"></i> Users</a>
        <a href="#users"><i class="fas fa-users"></i>Students Verification</a>
        
        <a href="#passes"><i class="fas fa-ticket-alt"></i> Payments Verification</a>
       
        <a href="#settings"><i class="fas fa-cogs"></i>Route Costs Management</a>
        <a href="admin-login.html" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- Content -->
    <div class="content">
        <h1>Welcome, Admin!</h1>
        <p>Manage your system efficiently.</p>

        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card p-4">
                    <h3><i class="fas fa-user"></i> Total Users</h3>
                    <p><?php echo $totalUsers; ?></p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-4">
                    <h3 ><i class="fas fa-money-bill"></i> Revenue</h3>
                    <p>₹<?php echo number_format($revenue, 2); ?></p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-4">
                    <h3 ><i class="fas fa-envelope"></i> Messages</h3>
                    <p><?php echo $totalMessages; ?></p>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card p-4">
                    <h3><i class="fas fa-times-circle"></i> Rejected Applications</h3>
                    <p><?php echo $rejectedApplications; ?></p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-4">
                    <h3><i class="fas fa-list"></i> Active Passes</h3>
                    <p><?php echo $activePasses; ?></p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-4">
                    <h3 ><i class="fas fa-clock"></i> Pending Requests</h3>
                    <p><?php echo $pendingRequests; ?></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
