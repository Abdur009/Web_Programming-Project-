<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Your Details – UniStay</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f5f6fa;
      min-height: 100vh;
    }

    nav {
      background: #d9234e;
      color: white;
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    nav .logo {
      font-weight: bold;
      font-size: 20px;
    }

    nav .nav-links a {
      color: white;
      text-decoration: none;
      margin-left: 25px;
      font-weight: 500;
      transition: opacity 0.2s ease;
    }

    nav .nav-links a:hover {
      opacity: 0.85;
    }

    .wrapper {
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 40px 20px;
    }

    .container {
      width: 100%;
      max-width: 1000px;
      background: #fff;
      border-radius: 16px;
      display: flex;
      overflow: hidden;
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }

    .form-left {
      flex: 1;
      padding: 50px 40px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .form-left h2 {
      font-size: 28px;
      margin-bottom: 10px;
      color: #222;
    }

    .form-left p {
      font-size: 14px;
      color: #666;
      margin-bottom: 30px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    label {
      font-size: 14px;
      color: #333;
      margin-bottom: 6px;
      display: block;
    }

    input {
      width: 100%;
      padding: 12px 15px;
      font-size: 14px;
      border: 1px solid #ccc;
      border-radius: 6px;
      background-color: #fafafa;
      transition: border 0.3s;
    }

    input:focus {
      border-color: #fbbf24;
      outline: none;
    }

    .submit-btn {
      background-color: #fbbf24;
      color: #000;
      padding: 12px 20px;
      border: none;
      border-radius: 6px;
      font-weight: bold;
      font-size: 15px;
      cursor: pointer;
      transition: background-color 0.3s ease;
      margin-top: 10px;
    }

    .submit-btn:hover {
      background-color: #f59e0b;
    }

    .back-btn-inline {
      display: inline-block;
      margin-top: 15px;
      background-color: #d9234e;
      color: white;
      padding: 10px 20px;
      border-radius: 6px;
      text-decoration: none;
      font-weight: bold;
      transition: background-color 0.3s ease;
    }

    .back-btn-inline:hover {
      background-color: #a71b3e;
    }

    .image-right {
      flex: 1;
      background: url('images/Screenshot 2025-06-18 011400.png') no-repeat center center;
      background-size: cover;
    }

    @media (max-width: 768px) {
      .container {
        flex-direction: column;
        border-radius: 12px;
      }

      .form-left {
        padding: 30px 25px;
      }

      .image-right {
        height: 200px;
      }

      nav {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
      }
    }
  </style>
</head>
<body>

  <!-- Navigation Bar -->
  <nav>
    <div class="logo">UniStay</div>
    <div class="nav-links">
      <a href="dashboard.php">Home</a>
      <a href="reviews.php">Review</a>
      <a href="gallery.php">Gallery</a>
    </div>
  </nav>

  <!-- Main Form Container -->
  <div class="wrapper">
    <div class="container">
      <div class="form-left">
        <h2>Your Details</h2>
        <p>Please fill in your information to confirm your booking</p>
        <form action="confirm.php" method="POST">
          <div class="form-group">
            <label for="student_name">Name</label>
            <input type="text" id="student_name" name="student_name" required>
          </div>
          <div class="form-group">
            <label for="matric_no">Matric No</label>
            <input type="text" id="matric_no" name="matric_no" required>
          </div>
          <div class="form-group">
            <label for="ic_no">IC No</label>
            <input type="text" id="ic_no" name="ic_no" required>
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
          </div>
          <div class="form-group">
            <label for="phone">Phone</label>
            <input type="tel" id="phone" name="phone" required>
          </div>
          <button type="submit" class="submit-btn">Confirm Booking</button>
          <br>
          <a href="javascript:history.back()" class="back-btn-inline">← Back</a>
        </form>
      </div>
      <div class="image-right"></div>
    </div>
  </div>
</body>
</html>
