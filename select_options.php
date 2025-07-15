<?php
session_start();
if (!isset($_GET['hostel']) || empty($_GET['hostel'])) {
    header("Location: dashboard.php");
    exit();
}
$selected = htmlspecialchars($_GET['hostel']);
$bgImage   = "images/" . strtolower($selected) . "-exterior.jpg";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Select Room Options for <?= $selected ?> - UniStay</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
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
      content:"";
      position:absolute;
      top:0; left:0;
      width:100%; height:100%;
      background:rgba(0,0,0,0.5);
      z-index:0;
    }

    nav {
      position: relative;
      z-index:2;
      background: rgba(217,35,78,0.95);
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: white;
      font-size: 16px;
      font-weight: 500;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    nav .logo {
      font-weight: 600;
      font-size: 20px;
    }

    nav a {
      color: white;
      text-decoration: none;
      margin-left: 25px;
      transition: opacity 0.3s ease;
    }

    nav a:hover {
      opacity: 0.8;
    }

    .container {
      position:relative;
      z-index:1;
      max-width:500px;
      margin:80px auto;
      background:rgba(255,255,255,0.85);
      padding:40px;
      border-radius:15px;
      backdrop-filter:blur(8px);
      box-shadow:0 8px 25px rgba(0,0,0,0.2);
      text-align:left;
    }

    h1 {
      font-size:1.8rem;
       color: #4a6fa5;
      text-align:center;
      margin-bottom:15px;
    }

    .hostel-display {
      text-align:center;
      font-weight:bold;
       color: #4a6fa5;
      margin-bottom:30px;
      font-size:1.2rem;
    }

    form div {
      margin-bottom:20px;
    }

    label {
      font-weight:600;
      display:block;
      margin-bottom:5px;
    }

    select {
      width:100%;
      padding:12px;
      border:1px solid #ccc;
      border-radius:8px;
      appearance:none;
      background:white url("data:image/svg+xml;utf8,<svg fill='%23333' viewBox='0 0 24 24'><path d='M7 10l5 5 5-5z'/></svg>") no-repeat right 10px center;
      cursor:pointer;
    }

    button {
      width:100%;
      padding:15px;
      background:#007bff;
      border:none;
      border-radius:8px;
      color:white;
      font-weight:bold;
      cursor:pointer;
      transition:0.3s;
    }

    button:hover {
      background:#0056b3;
    }

    .back {
      margin-top: 20px;
      text-align: center;
    }

    .back-btn-inline {
      display: inline-block;
      background-color: #d9234e;
      color: white;
      padding: 12px 24px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: bold;
      transition: background-color 0.3s ease;
    }

    .back-btn-inline:hover {
      background-color: #a71b3e;
    }

    @media(max-width:600px){
      .container {
        margin:50px 20px;
        padding:30px;
      }

      nav {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
      }
    }
  </style>
</head>
<body>
  <nav>
    <div class="logo">UniStay</div>
    <div>
      <a href="dashboard.php">Home</a>
      <a href="reviews.php">Review</a>
      <a href="gallery.php">Gallery</a>
    </div>
  </nav>

  <div class="container">
    <h1>Select Room Options</h1>
    <div class="hostel-display">For: <?= $selected ?></div>
    <form action="select_room.php" method="POST">
      <input type="hidden" name="hostel" value="<?= $selected ?>">
      <div>
        <label>Choose Room Type:</label>
        <select name="room" required>
          <option value="">-- Select --</option>
          <option value="Single">Single Room</option>
          <option value="Double">Double Room</option>
        </select>
      </div>
      <div>
        <label>Choose Duration:</label>
        <select name="duration" required>
          <option value="">-- Select --</option>
          <option value="1 Semester">1 Semester</option>
          <option value="2 Semesters">2 Semesters</option>
        </select>
      </div>
      <button type="submit">Book Now</button>
    </form>
    <div class="back">
      <a href="javascript:history.back()" class="back-btn-inline">← Back</a>
    </div>
  </div>
</body>
</html>
