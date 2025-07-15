<?php
session_start();

$host = "localhost";
$db = "hostel_booking";
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE username=? AND password=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
  $user = $result->fetch_assoc();

  $_SESSION['user_id'] = $user['id'];          // ✅ required for profile.php
  $_SESSION['username'] = $user['username'];   // still good to have
  $_SESSION['name'] = $user['name'];           // optional, for display

  header("Location: dashboard.php");
  exit();
} else {
  echo "Invalid login credentials.";
}

$stmt->close();
$conn->close();
?>
