<?php
session_start();
include 'db_connection.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please log in first!'); window.location.href='login.html';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch current data from database
$sql = "SELECT * FROM students WHERE user_id = '$user_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    echo "<script>alert('No registration found. Please register first.'); window.location.href='register.php';</script>";
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $email = $conn->real_escape_string($_POST['email']);
    $dob = $conn->real_escape_string($_POST['dob']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $collage_name = $conn->real_escape_string($_POST['collage_name']);
    
    

    // File Upload Handling
    $upload_dir = "uploads/";

    function uploadFile($file_input_name, $upload_dir, $existing_file) {
        if (!empty($_FILES[$file_input_name]["name"])) { // Upload only if file selected
            $file_tmp_name = $_FILES[$file_input_name]["tmp_name"];
            $file_name = time() . "_" . basename($_FILES[$file_input_name]["name"]);
            $target_file = $upload_dir . $file_name;

            if (move_uploaded_file($file_tmp_name, $target_file)) {
                return $target_file;
            }
        }
        return $existing_file; // Return existing file if no new file is uploaded
    }

    // Keep old file if new file is not uploaded
    $passport_photo = uploadFile("passport_photo", $upload_dir, $row['passport_photo']);
    $id_card = uploadFile("id_card", $upload_dir, $row['id_card']);
    $bonafide_certificate = uploadFile("bonafide_certificate", $upload_dir, $row['bonafide_certificate']);

    // Update query
    $sql = "UPDATE students SET 
                name = '$name', 
                phone = '$phone', 
                email = '$email', 
                dob = '$dob', 
                gender = '$gender',
                collage_name = '$collage_name',
                passport_photo = '$passport_photo', 
                id_card = '$id_card', 
                bonafide_certificate = '$bonafide_certificate',
                status = 'pending', 
                rejection_reason = NULL
            WHERE user_id = '$user_id'";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Data Updated Successfully! Please wait for admin approval.'); window.location.href='dashboard.php';</script>";
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MSRTC BUS PASS - Registration</title>
    
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
        
        .registration-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            flex-grow: 1;
            padding: 40px 20px;
        }
        
        .registration-container h2 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            color: var(--primary);
            text-align: center;
            margin-bottom: 30px;
            position: relative;
        }
        
        .registration-container h2:after {
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
        
        form {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 30px rgba(0, 86, 179, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 700px;
            border-top: 5px solid var(--primary);
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
        
        .form-label b {
            color: var(--dark);
        }
        
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            padding: 12px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s;
            margin-top: 20px;
        }
        
        .btn-primary:hover {
            background-color: #004494;
            border-color: #004494;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 86, 179, 0.3);
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
        
        @media (max-width: 768px) {
            form {
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
                        <a class="nav-link" href="../index.html#contact"><i class="fas fa-envelope"></i> Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Registration Form -->
    <div class="registration-container">
        <h2>Registration</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label"><b>Full Name</b></label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><b>Phone</b></label>
                <input type="tel" name="phone" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><b>Email</b></label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><b>Date of Birth</b></label>
                <input type="date" name="dob" class="form-control" id="dob" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><b>Age</b></label>
                <input type="number" name="age" class="form-control" id="age" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label"><b>Gender</b></label>
                <select class="form-control" name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label"><b>School/Collage Name</b></label>
                <input type="text" name="collage_name" class="form-control" id="Collage" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><b>Passport Photo</b></label>
                <input type="file" class="form-control" name="passport_photo" accept="image/*" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><b>ID Card</b></label>
                <input type="file" class="form-control" name="id_card" accept="image/*,application/pdf" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><b>Bonafide Certificate</b></label>
                <input type="file" class="form-control" name="bonafide_certificate" accept="image/*,application/pdf" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2025 Maharashtra State Road Transport Corporation. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Age Calculation Script -->
    <script>
        document.getElementById('dob').addEventListener('change', function() {
            const dob = new Date(this.value);
            const today = new Date();
            let age = today.getFullYear() - dob.getFullYear();
            const monthDiff = today.getMonth() - dob.getMonth();
            
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
                age--;
            }
            
            document.getElementById('age').value = age;
        });
    </script>
</body>

</html>
