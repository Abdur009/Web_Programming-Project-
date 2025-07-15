<?php
session_start();
if (!isset($_SESSION['booking'])) {
    header('Location: dashboard.php');
    exit();
}
$booking = $_SESSION['booking'];
$duration = intval($booking['duration']);
$hostel = strtolower($booking['hostel']);
$room = strtolower($booking['room']);
$price = ($room === 'single') ? 1670 * $duration : 1500 * $duration;

// Images
$imagePath = "images/{$hostel}_{$room}.jpg";
if (!file_exists($imagePath)) {
    $imagePath = "images/default.jpg";
}
$bgImage = "images/{$hostel}-exterior.jpg";
if (!file_exists($bgImage)) {
    $bgImage = "images/default_bg.jpg";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Room Details - UniStay</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <style>
    * {
      margin: 0; padding: 0; box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: url('<?= $bgImage ?>') no-repeat center center fixed;
      background-size: cover;
      position: relative;
      min-height: 100vh;
      color: #333;
    }

    body::before {
      content: "";
      position: absolute;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.5);
      z-index: 0;
    }

    nav {
      position: relative;
      z-index: 2;
      background: rgba(217, 35, 78, 0.95);
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: white;
    }

    .logo {
      font-size: 1.4rem;
      font-weight: bold;
    }

    .nav-right a {
      color: white;
      text-decoration: none;
      margin-left: 25px;
      font-weight: 600;
    }

    .nav-right a:hover {
      opacity: 0.8;
    }

    .container {
      position: relative;
      z-index: 1;
      max-width: 900px;
      margin: 60px auto;
      background: rgba(255, 255, 255, 0.85);
      padding: 30px;
      border-radius: 16px;
      backdrop-filter: blur(8px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.25);
      display: flex;
      gap: 30px;
      align-items: center;
      transition: transform 0.3s ease;
    }

    .container:hover {
      transform: translateY(-3px);
    }

    .room-img {
      width: 50%;
      height: 350px;
      object-fit: cover;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .room-details {
      width: 50%;
    }

    .room-details h2 {
      color: #4a6fa5;
      margin-bottom: 15px;
    }

    .room-details p {
      font-size: 16px;
      margin: 10px 0;
    }

    .room-details p i {
      margin-right: 10px;
      color: #4a6fa5;
    }

    .room-details strong {
      color: #222;
    }

    button {
  margin-top: 25px;
  background-color: #007bff; /* Same as Book Now */
  color: white;
  padding: 12px 24px;
  font-size: 16px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: background 0.3s ease, transform 0.2s ease;
}

button:hover {
  background-color: #0056b3; /* darker hover variant */
  transform: scale(1.05);
}


    .back-btn-inline {
      display: inline-block;
      margin-top: 20px;
      background-color: #d9234e;
      color: white;
      padding: 10px 20px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: bold;
      transition: background-color 0.3s ease;
    }

    .back-btn-inline:hover {
      background-color: #a71b3e;
    }

    @media (max-width: 768px) {
      .container {
        flex-direction: column;
        text-align: center;
        padding: 25px;
        margin: 30px 20px;
      }

      .room-img, .room-details {
        width: 100%;
      }
    }
  </style>
</head>
<body>
  <nav>
    <div class="logo">UniStay</div>
    <div class="nav-right">
      <a href="dashboard.php">Home</a>
      <a href="reviews.php">Review</a>
      <a href="gallery.php">Gallery</a>
    </div>
  </nav>

  <div class="container">
    <img src="<?= htmlspecialchars($imagePath) ?>" alt="Room Image" class="room-img">
    <div class="room-details">
      <h2><?= htmlspecialchars($booking['hostel']) ?> – <?= htmlspecialchars(ucfirst($booking['room'])) ?> Room</h2>
      <p><i class="fas fa-bed"></i><strong> Comfortable bed with study space</strong></p>
      <p><i class="fas fa-wifi"></i><strong> Free high speed WI-FI</strong></p>
      <p><i class="fas fa-shield-alt"></i><strong> 24/7 Security</strong></p>
      <p><i class="fas fa-parking"></i><strong> Free parking</strong></p>
      <p><strong>Duration:</strong> <?= htmlspecialchars($duration) ?> Semester(s)</p>
      <p><strong>Total Price:</strong> RM <?= number_format($price, 2) ?></p>
      <form method="POST" action="student.php">
        <input type="hidden" name="price" value="<?= $price ?>">
        <button type="submit">Confirm Selection</button>
      </form>
      <a href="javascript:history.back()" class="back-btn-inline">← Back</a>
    </div>
  </div>

</body>
</html>
