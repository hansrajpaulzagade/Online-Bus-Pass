<?php
session_start();
include 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please log in first!'); window.location.href='../HTML File/login.html';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch existing data for the user
$stmt = $conn->prepare("SELECT source, destination, valid_until, cost, transaction_id FROM payments WHERE user_id = ? ORDER BY id DESC LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($source, $destination, $valid_until, $cost, $transaction_id);
$stmt->fetch();
$stmt->close();

// If no previous data exists, redirect to the apply page
if (!$source) {
    echo "<script>alert('No previous application found. Please apply first.'); window.location.href='bus_pass.php';</script>";
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $source = $_POST['source'];
    $destination = $_POST['destination'];
    $valid_until = $_POST['valid_until'];
    $cost = str_replace('₹', '', $_POST['cost']); // Remove ₹ symbol
    $transaction_id = $_POST['transaction_id'];
    $payment_status = 'pending'; // Reset status to pending after reapplying

    // Validate input
    if (empty($source) || empty($destination) || empty($valid_until) || empty($cost) || empty($transaction_id)) {
        echo "<script>alert('All fields are required!'); window.location.href='reapply.php';</script>";
        exit();
    }

    // Update existing entry instead of inserting a new one
    $stmt = $conn->prepare("UPDATE payments SET source=?, destination=?, valid_until=?, cost=?, transaction_id=?, payment_status=? WHERE user_id=?");
    $stmt->bind_param("sssdssi", $source, $destination, $valid_until, $cost, $transaction_id, $payment_status, $user_id);

    if ($stmt->execute()) {
        echo "<script>alert('Bus pass reapplication updated successfully!'); window.location.href='dashboard.php';</script>";
    } else {
        echo "<script>alert('Error updating application.'); window.location.href='reapply.php';</script>";
    }

    $stmt->close();
    $conn->close();
    exit();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MSRTC BUS PASS - Create Pass</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --primary: #0056b3;
            --secondary: #ffc107;
            --dark: #1a2b50;
            --light: #f8f9fa;
            --accent: #e83e8c;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, rgba(0,86,179,0.05) 0%, rgba(255,255,255,1) 100%);
            color: var(--dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .navbar {
            background: linear-gradient(135deg, var(--primary) 0%, var(--dark) 100%);
            box-shadow: 0 4px 20px rgba(0, 86, 179, 0.15);
            padding: 15px 0;
        }
        
        .navbar-brand {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            font-size: 1.8rem;
            color: white !important;
            display: flex;
            align-items: center;
        }
        
        .navbar-brand img {
            height: 50px;
            margin-right: 15px;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
        }
        
        .pass-container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-grow: 1;
            padding: 40px 20px;
        }
        
        .pass-box {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 30px rgba(0, 86, 179, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 600px;
            border-top: 5px solid var(--primary);
        }
        
        .pass-box h2 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            color: var(--primary);
            text-align: center;
            margin-bottom: 30px;
            position: relative;
        }
        
        .pass-box h2:after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background: var(--secondary);
            border-radius: 2px;
        }
        
        .form-control {
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(0, 86, 179, 0.25);
        }
        
        .form-control[readonly] {
            background-color: #f8f9fa;
        }
        
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            padding: 12px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background-color: #004494;
            border-color: #004494;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 86, 179, 0.3);
        }
        
        .payment-notice {
            background-color: #fff8e1;
            border-left: 4px solid var(--secondary);
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        
        footer {
            background-color: var(--dark);
            color: white;
            padding: 20px 0;
            text-align: center;
            margin-top: auto;
        }
        
        footer p {
            margin: 0;
            font-size: 0.9rem;
        }
        
        @media (max-width: 576px) {
            .pass-box {
                padding: 30px 20px;
            }
        }
    </style>
</head>

<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="../index.html">
                <img src="../Images/logo.jpg" alt="MSRTC Logo"> MSRTC BUS PASS
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.html"><i class="fas fa-home"></i> Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../index.html#about"><i class="fas fa-info-circle"></i> About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../index.html#services"><i class="fas fa-cogs"></i> Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../HTML File/contact.html"><i class="fas fa-envelope"></i> Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Bus Pass Form -->
    <div class="pass-container">
        <div class="pass-box">
            <h2>Bus Pass</h2>
            <form action="../PHP File/createpass.php" method="POST">
                <div class="mb-3">
                    <label for="source" class="form-label">Source</label>
                    <select class="form-control" name="source" id="source" required onchange="fetchCost()">
                        <option value="">Select Source</option>
                        <option value="City A">City A</option>
                        <option value="City B">City B</option>
                        <option value="City C">City C</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="destination" class="form-label">Destination</label>
                    <select class="form-control" name="destination" id="destination" required onchange="fetchCost()">
                        <option value="">Select Destination</option>
                        <option value="City A">City A</option>
                        <option value="City B">City B</option>
                        <option value="City C">City C</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="validity" class="form-label">Validity</label>
                    <input type="date" class="form-control" name="valid_until" id="validity" readonly required>
                </div>

                <!-- Cost and Payment Instruction -->
                <div class="mb-3 text-center">
                    <label for="cost" class="form-label">Total Cost</label>
                    <input type="text" class="form-control text-center fw-bold" name="cost" id="cost" readonly required>

                    <div class="payment-notice">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                        <span class="fw-bold">Please verify the amount and make the payment. Enter the correct Transaction ID to avoid payment failure!</span>
                    </div>

                    <!-- QR Code for Payment -->
                    <label class="form-label">Scan to Pay</label>
                    <img src="../Images/qr-code.png" alt="QR Code" class="img-fluid d-block mx-auto" style="max-width: 200px;">
                </div>

                <!-- Enter Transaction ID -->
                <div class="mb-3">
                    <label for="transaction_id" class="form-label">Enter Transaction ID</label>
                    <input type="text" class="form-control" name="transaction_id" id="transaction_id" placeholder="Enter your transaction ID" required>
                </div>

                <button type="submit" class="btn btn-primary w-100 mt-3">Submit Payment</button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2025 Maharashtra State Road Transport Corporation. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


<script>
document.addEventListener("DOMContentLoaded", function () {
    fetchRoutes();
    setValidityDate();
});

// Fetch Routes from Server
function fetchRoutes() {
    let xhr = new XMLHttpRequest();
    xhr.open("GET", "../PHP File/fetch_routes.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let data = JSON.parse(xhr.responseText);
            populateDropdown("source", data.sources);
            populateDropdown("destination", data.destinations);
        }
    };
    xhr.send();
}

// Populate Dropdowns
function populateDropdown(id, items) {
    let dropdown = document.getElementById(id);
    dropdown.innerHTML = '<option value="">Select ' + id.charAt(0).toUpperCase() + id.slice(1) + '</option>';
    items.forEach(item => {
        let option = document.createElement("option");
        option.value = item;
        option.textContent = item;
        dropdown.appendChild(option);
    });
}

// Fetch Cost and Enable/Disable Submit Button
function fetchCost() {
    let source = document.getElementById("source").value;
    let destination = document.getElementById("destination").value;
    let costField = document.getElementById("cost");
    let submitButton = document.querySelector("button[type='submit']");
    
    // Reset cost field and disable submit button initially
    costField.value = "";
    submitButton.disabled = true;

    if (source === destination && source !== "") {
        alert("Source and Destination cannot be the same!");
        document.getElementById("destination").value = "";
        return;
    }

    if (source && destination) {
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "../PHP File/get_cost.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                let cost = xhr.responseText.trim();
                costField.value = (cost === "Not Available") ? "" : cost;

                if (cost !== "Not Available" && cost !== "") {
                    submitButton.disabled = false; // Enable button if cost is valid
                } else {
                    submitButton.disabled = true; // Keep button disabled if cost is unavailable
                    alert("Sorry, cost not available for this route. Please select a different route.");
                }
            }
        };
        xhr.send("source=" + source + "&destination=" + destination);
    }
}

// Set Default Validity Date
function setValidityDate() {
    let validityField = document.getElementById("validity");
    let today = new Date();
    let nextMonth = new Date();
    nextMonth.setMonth(today.getMonth() + 1);
    validityField.valueAsDate = nextMonth;
}
    </script>
</body>

</html>