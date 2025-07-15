<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['booking'])) {
    header('Location: dashboard.php');
    exit();
}

$required_fields = ['student_name', 'matric_no', 'ic_no', 'email', 'phone'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
        die("Error: Missing required field '$field'.");
    }
}

$b = $_SESSION['booking'];
$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;

$hostel = $b['hostel'];
$room = $b['room'];
$duration = $b['duration'];
$student_name = $_POST['student_name'];
$matric_no = $_POST['matric_no'];
$ic_no = $_POST['ic_no'];
$email = $_POST['email'];
$phone = $_POST['phone'];

// Connect to DB
$db = new mysqli('localhost', 'root', '', 'hostel_booking');
if ($db->connect_error) {
    die("Database connection failed: " . $db->connect_error);
}

// Get user_id from username
$user_id = null;
if ($username) {
    $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();

    if (!$user_id) {
        die("Error: User not found.");
    }
} else {
    die("Error: Not logged in.");
}

// Insert into bookings table
$stmt = $db->prepare("INSERT INTO bookings (user_id, hostel, room_type, duration) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isss", $user_id, $hostel, $room, $duration);
if (!$stmt->execute()) {
    die("Booking insert failed: " . $stmt->error);
}
$booking_id = $stmt->insert_id;
$stmt->close();

// Insert into students table
$stmt = $db->prepare("INSERT INTO students (booking_id, full_name, matric_number, ic_number, email, phone_number) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssss", $booking_id, $student_name, $matric_no, $ic_no, $email, $phone);
if (!$stmt->execute()) {
    die("Student insert failed: " . $stmt->error);
}
$stmt->close();
$db->close();


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Booking Confirmation</title>
 
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f4f6f9;
      margin: 0;
      padding: 0;
    }

    .confirmation-container {
      max-width: 600px;
      margin: 50px auto;
      background-color: #ffffff;
      padding: 30px 40px;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .confirmation-container h2 {
      color: #2a7ae2;
      font-size: 28px;
      margin-bottom: 10px;
    }

    .confirmation-container p {
      font-size: 16px;
      color: #333;
    }

    .booking-details {
      margin-top: 20px;
      padding: 0;
      list-style: none;
    }

    .booking-details li {
      padding: 10px 0;
      border-bottom: 1px solid #eee;
    }

    .booking-details strong {
      display: inline-block;
      width: 130px;
      color: #555;
    }

    .button-back {
      margin-top: 30px;
      display: inline-block;
      background-color: #2a7ae2;
      color: white;
      padding: 10px 20px;
      border-radius: 5px;
      text-decoration: none;
    }

    .button-back:hover {
      background-color: #1d5ec9;
    }
  </style>
</head>
<body>

<div class="confirmation-container">
  <h2>🎉 Booking Confirmed!</h2>
  <p>Thank you for your reservation. Below are your booking details:</p>

  <ul class="booking-details">
    <li><strong>Hostel:</strong> <?= htmlspecialchars($b['hostel']) ?></li>
    <li><strong>Room Type:</strong> <?= htmlspecialchars($b['room']) ?></li>
    <li><strong>Duration:</strong> <?= htmlspecialchars($b['duration']) ?></li>
    <li><strong>Full Name:</strong> <?= htmlspecialchars($_POST['student_name']) ?></li>
    <li><strong>Matric No:</strong> <?= htmlspecialchars($_POST['matric_no']) ?></li>
    <li><strong>IC No:</strong> <?= htmlspecialchars($_POST['ic_no']) ?></li>
    <li><strong>Email:</strong> <?= htmlspecialchars($_POST['email']) ?></li>
    <li><strong>Phone:</strong> <?= htmlspecialchars($_POST['phone']) ?></li>
  </ul>

  <a class="button-back" href="dashboard.php">← Back to Dashboard</a>
</div>

</body>
</html>
