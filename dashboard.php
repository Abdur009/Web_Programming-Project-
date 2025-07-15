<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

require 'db.php';

$user_id = $_SESSION['user_id'];
$profile_pic_path = 'images/user.png';

$stmt = $conn->prepare("SELECT profile_pic FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if (!empty($user['profile_pic']) && file_exists('uploads/' . $user['profile_pic'])) {
        $profile_pic_path = 'uploads/' . htmlspecialchars($user['profile_pic']);
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>UniStay - Find Your Perfect Hostel</title>
  <!-- Preload critical resources -->
  <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"></noscript>
  <link rel="stylesheet" href="dashboard.css">
  <!-- Preload hero image -->
  <link rel="preload" href="images/photo-1623631484725-fef26b75b402.jpg" as="image">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f9f9f9;
      color: #333;
      line-height: 1.6;
    }
    nav {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: #d9234e;
      padding: 12px 24px;
      color: white;
      position: sticky;
      top: 0;
      z-index: 1000;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
  .logo {
      font-size: 1.8rem;
      font-weight: bold;
    }
    .nav-right {
      display: flex;
      align-items: center;
      gap: 20px;
    }
    .nav-right a {
      color: white;
      text-decoration: none;
      font-weight: bold;
      transition: all 0.3s ease;
    }
    .nav-right a:hover {
      opacity: 0.9;
      transform: translateY(-2px);
    }
    .profile-dropdown {
      position: relative;
    }
    .profile-pic {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      border: 2px solid #fff;
      object-fit: cover;
      cursor: pointer;
      transition: transform 0.3s ease;
    }
    .profile-pic:hover {
      transform: scale(1.1);
    }
    .dropdown-content {
      display: none;
      position: absolute;
      right: 0;
      top: 50px;
      background: white;
      color: black;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      z-index: 1000;
      min-width: 200px;
      overflow: hidden;
    }
    .dropdown-content a {
      display: block;
      padding: 12px 16px;
      color: #333;
      text-decoration: none;
      font-weight: bold;
      transition: all 0.3s ease;
    }
    .dropdown-content a:hover {
      background-color: #f2f2f2;
      padding-left: 20px;
    }
    .hero-main {
      background: url('images/photo-1623631484725-fef26b75b402.jpg') center center/cover no-repeat;
      position: relative;
      padding: 150px 20px 120px;
      text-align: center;
      color: white;
    }
    .hero-main::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: rgba(0, 0, 0, 0.5);
      z-index: 0;
    }
    .hero-content {
      position: relative;
      z-index: 1;
      max-width: 700px;
      margin: 0 auto;
    }
    .hero-content h1 {
      font-size: 3rem;
      margin-bottom: 20px;
      font-weight: bold;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    }
    .hero-content p {
      font-size: 1.2rem;
      margin-bottom: 30px;
      text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
    }
    .btn-explore {
      background-color: #d9234e;
      color: white;
      padding: 15px 30px;
      border: none;
      border-radius: 30px;
      font-size: 1.1rem;
      cursor: pointer;
      text-decoration: none;
      display: inline-block;
      transition: all 0.3s ease;
      margin-top: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
    .btn-explore:hover {
      background-color: #a71b3e;
      transform: translateY(-3px);
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
    }
    .features-section {
      display: flex;
      justify-content: center;
      align-items: stretch;
      flex-wrap: wrap;
      gap: 20px;
      padding: 40px 20px;
      background: #fff;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
      margin-top: -80px;
      position: relative;
      z-index: 10;
      border-radius: 10px;
      max-width: 1200px;
      margin-left: auto;
      margin-right: auto;
    }
    .feature-card {
      background: white;
      padding: 25px;
      border-radius: 8px;
      flex: 1 1 22%;
      min-width: 250px;
      max-width: 280px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      text-align: left;
      transition: all 0.3s ease;
    }
    .feature-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }
    .feature-card h3 {
      font-size: 1.3rem;
      margin-bottom: 10px;
      font-weight: 600;
      color: #d9234e;
    }
    .feature-card p {
      font-size: 1rem;
      color: #555;
    }
    .available-hostels-section {
      padding: 60px 20px;
      background: #f9f9f9;
      text-align: center;
    }
    .available-hostels-section h2 {
      font-size: 2.5rem;
      margin-bottom: 50px;
      font-weight: bold;
      color: #333;
      position: relative;
      display: inline-block;
    }
    .available-hostels-section h2::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
      width: 80px;
      height: 4px;
      background: #d9234e;
      border-radius: 2px;
    }
    .hostel-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 30px;
      max-width: 1000px;
      margin: 0 auto;
    }
    .hostel-card {
      background: white;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      text-align: left;
      transition: all 0.3s ease;
      position: relative;
    }
    .hostel-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
    }
    .hostel-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 5px;
      background: #d9234e;
    }
    .hostel-card img {
      width: 100%;
      height: 200px;
      object-fit: cover;
      transition: transform 0.5s ease;
    }
    .hostel-card:hover img {
      transform: scale(1.05);
    }
    .hostel-info {
      padding: 20px;
    }
    .hostel-info h3 {
      font-size: 1.5rem;
      margin-bottom: 10px;
      font-weight: 600;
      color: #333;
    }
    .hostel-info p {
      font-size: 1rem;
      color: #666;
      margin-bottom: 20px;
    }
    .btn-details {
      background-color: #007bff;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      text-decoration: none;
      display: inline-block;
      font-size: 1rem;
      transition: all 0.3s ease;
      margin-right: 10px;
    }
    .btn-details:hover {
      background-color: #0056b3;
      transform: translateY(-2px);
    }
    .btn-location {
      background-color: #28a745;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      text-decoration: none;
      display: inline-block;
      font-size: 1rem;
      transition: all 0.3s ease;
      cursor: pointer;
    }
    .btn-location:hover {
      background-color: #218838;
      transform: translateY(-2px);
    }
    #chatbot-container {
      position: fixed;
      bottom: 80px;
      right: 20px;
      width: 350px;
      height: 450px;
      background: white;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
      display: none;
      z-index: 1000;
      border-radius: 10px;
      overflow: hidden;
    }
    #open-chatbot {
      position: fixed;
      bottom: 20px;
      right: 20px;
      background: #d9234e;
      color: white;
      border: none;
      border-radius: 50%;
      width: 60px;
      height: 60px;
      font-size: 1.5rem;
      cursor: pointer;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
      z-index: 1001;
      display: flex;
      justify-content: center;
      align-items: center;
      transition: all 0.3s ease;
    }
    #open-chatbot:hover {
      background: #a71b3e;
      transform: scale(1.1);
    }
    .hostel-grid.hidden {
      opacity: 0.2;
    }
    .map-section {
      position: fixed;
      top: 0;
      right: -100%;
      width: 100%;
      height: 100%;
      background: white;
      box-shadow: -10px 0 30px rgba(0, 0, 0, 0.1);
      z-index: 1000;
      transition: right 0.6s ease;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }
    .map-section.visible {
      right: 0;
    }
    .map-container {
      width: 90%;
      height: 70%;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }
    #close-map {
      margin-top: 20px;
      padding: 10px 20px;
      background: #d9234e;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    #close-map:hover {
      background: #a71b3e;
      transform: translateY(-2px);
    }
    .search-container {
      position: absolute;
      top: 20px;
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      align-items: center;
      width: 90%;
      max-width: 600px;
      background: rgba(255, 255, 255, 0.95);
      border-radius: 30px;
      padding: 10px 20px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
      z-index: 2;
    }
    #search-bar {
      flex: 1;
      border: none;
      outline: none;
      font-size: 1rem;
      padding: 8px 10px;
      background: transparent;
    }
    #search-bar::placeholder {
      color: #666;
    }
    #search-button {
      border: none;
      background-color: #d9234e;
      color: white;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      display: flex;
      justify-content: center;
      align-items: center;
      font-size: 1.2rem;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    #search-button:hover {
      background-color: #a71b3e;
      transform: scale(1.1);
    }
    .no-results {
      grid-column: 1 / -1;
      text-align: center;
      padding: 40px;
      color: #666;
      font-size: 1.2rem;
    }

   .search-container {
  position: absolute;
  top: 20px;
  left: 50%;
  transform: translateX(-50%);
  width: 90%;
  max-width: 600px;
  background: rgba(255, 255, 255, 0.95);
  border-radius: 30px;
  padding: 10px 20px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
  z-index: 2;
  display: flex;
  align-items: center;
}

.search-suggestions {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background: white;
  border-radius: 0 0 10px 10px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
  max-height: 300px;
  overflow-y: auto;
  display: none;
  z-index: 1001;
}

.search-suggestions ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.search-suggestions li {
  padding: 10px 15px;
  cursor: pointer;
  border-bottom: 1px solid #eee;
  transition: background 0.3s ease;
}

.search-suggestions li:hover {
  background-color: #f9f9f9;
}

.search-suggestions li {
  color: #333; /* Dark grey text */
}
/* Chatbot Styles */
 #chatbot {
  position: fixed;
  bottom: 20px;
  right: 20px;
  width: 350px;
  height: 500px;
  background: #fff;
  border-radius: 15px;
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
  display: none;
  flex-direction: column;
  overflow: hidden;
 z-index: 1000; /* Add or increase this value */
}
    #chatbot-header {
      background: #007bff;
      color: #fff;
      padding: 15px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 18px;
      font-weight: bold;
    }

    #chatbot-header .close-chatbot {
      background: transparent;
      border: none;
      color: #fff;
      font-size: 20px;
      cursor: pointer;
    }

    #chat-log {
      flex: 1;
      padding: 15px;
      overflow-y: auto;
      background: #f9f9f9;
    }

    #chat-log p {
      margin: 10px 0;
      line-height: 1.5;
    }

    #chat-log .bot {
      text-align: left;
      background: #e9f5ff;
      color: #007bff;
      padding: 10px;
      border-radius: 8px;
      max-width: 80%;
      display: inline-block;
    }

    #chat-log .user {
      text-align: right;
      background: #dff1d6;
      color: #333;
      padding: 10px;
      border-radius: 8px;
      max-width: 80%;
      display: inline-block;
    }

    #chat-input {
      display: flex;
      justify-content: space-between;
      padding: 15px;
      background: #f4f4f4;
      border-top: 1px solid #ddd;
    }

    #chat-input input {
      flex: 1;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
      margin-right: 10px;
    }

    #chat-input button {
      padding: 10px 15px;
      background: #007bff;
      color: #fff;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s ease-in-out;
    }

    #chat-input button:hover {
      background: #0056b3;
    }

    #chatbot-toggle {
      position: fixed;
      bottom: 20px;
      right: 20px;
      background: #007bff;
      color: #fff;
      border: none;
      padding: 15px;
      border-radius: 50%;
      cursor: pointer;
      font-size: 22px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    #chatbot-toggle:hover {
      background: #0056b3;
    }

    #chat-log .user {
  /* text-align: right; remove this as we'll use margin-left: auto */
  background: #dff1d6;
  color: #333;
  padding: 10px;
  border-radius: 8px;
  max-width: 80%;
  /* display: inline-block; change this to display: block or remove it */
  display: block; /* Make it a block element to take full width and apply margins */
  margin-left: auto; /* Push the user message to the right */
  margin-right: 0;
}

#chat-log .bot {
  text-align: left;
  background: #e9f5ff;
  color: #007bff;
  padding: 10px;
  border-radius: 8px;
  max-width: 80%;
  display: block; /* Also make bot messages block to occupy their own line */
  margin-right: auto; /* Ensure bot messages stay on the left */
  margin-left: 0;
}
.visually-hidden {
  position: absolute !important;
  height: 1px; width: 1px;
  overflow: hidden;
  clip: rect(1px, 1px, 1px, 1px);
  white-space: nowrap;
}

  </style>
<!-- Add appropriate language and title -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>UniStay Hostel Booking</title>
  <!-- Include FontAwesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body>
  <nav role="navigation" aria-label="Main navigation">
    <div class="logo"><b>UniStay</b></div>
    <div class="nav-right">
      <a href="dashboard.php">Home</a>
      <a href="reviews.php">Review</a>
      <a href="gallery.php">Gallery</a>
      <div class="profile-dropdown" id="profileDropdown">
        <img src="<?= $profile_pic_path ?>" alt="Profile picture" class="profile-pic" id="profilePic" tabindex="0" aria-haspopup="true" aria-expanded="false" />
        <div class="dropdown-content" id="dropdownMenu" role="menu" aria-label="Profile menu">
          <a href="profile.php" role="menuitem"><i class="fas fa-user" aria-hidden="true"></i> <span>Profile</span></a>
          <a href="booking_history.php" role="menuitem"><i class="fas fa-history" aria-hidden="true"></i> <span>Booking History</span></a>
          <a href="logout.php" role="menuitem"><i class="fas fa-sign-out-alt" aria-hidden="true"></i> <span>Logout</span></a>
        </div>
      </div>
    </div>
  </nav>

  <main>
    <section class="hero-main" aria-label="Hero section">
      <div class="search-container" role="search">
        <label for="search-bar" class="visually-hidden">Search hostels</label>
        <input type="text" id="search-bar" placeholder="Search hostels by name..." aria-label="Search hostels by name" />
        <button id="search-button" aria-label="Submit search"><i class="fas fa-search" aria-hidden="true"></i></button>
        <div class="search-suggestions" id="search-suggestions" role="listbox" aria-label="Search suggestions">
          <ul id="suggestion-list"></ul>
        </div>
      </div>

      <div class="hero-content">
        <h1>Find Your Perfect Hostel at UTM</h1>
        <p>Comfortable and convenient on-campus accommodation for your academic journey.</p>
        <a href="#available-hostels" class="btn-explore">Explore Hostels</a>
      </div>
    </section>

    <section class="features-section" aria-label="Features">
      <div class="feature-card">
        <h3><i class="fas fa-calendar-check" aria-hidden="true"></i> Easy Booking</h3>
        <p>Streamlined process to reserve your room in just a few clicks.</p>
      </div>
      <div class="feature-card">
        <h3><i class="fas fa-headset" aria-hidden="true"></i> 24/7 Support</h3>
        <p>Ensures 24/7 support and verified room listings in UTM hostels.</p>
      </div>
      <div class="feature-card">
        <h3><i class="fas fa-home" aria-hidden="true"></i> Comfort and Convenience</h3>
        <p>All rooms come with essential facilities. Select based on your preferred hostel lifestyle.</p>
      </div>
      <div class="feature-card">
        <h3><i class="fas fa-calendar-alt" aria-hidden="true"></i> Flexible Stay Options</h3>
        <p>Book for 1 or 2 semesters with easy steps ensuring affordability, comfort, and convenience.</p>
      </div>
    </section>

    <section id="available-hostels" class="available-hostels-section" aria-labelledby="hostel-heading">
      <h2 id="hostel-heading">Available Hostels</h2>
      <div class="hostel-grid" id="hostel-grid">
        <article class="hostel-card">
          <img src="images/kdoj-exterior.jpg" alt="Exterior of Kolej Dato' Onn Jaafar">
          <div class="hostel-info">
            <h3>Kolej Dato' Onn Jaafar (KDOJ)</h3>
            <p>A vibrant hostel offering diverse room types and a strong community spirit.</p>
            <a href="select_options.php?hostel=KDOJ" class="btn-details" aria-label="View details about KDOJ hostel"><i class="fas fa-info-circle" aria-hidden="true"></i> View Details</a>
            <button class="btn-location" onclick="showMap('KDOJ')" aria-label="View map location for KDOJ"><i class="fas fa-map-marker-alt" aria-hidden="true"></i> View Location</button>
          </div>
        </article>

        <article class="hostel-card">
          <img src="images/ktr-exterior.jpg" alt="Exterior of Kolej Tun Razak">
          <div class="hostel-info">
            <h3>Kolej Tun Razak (KTR)</h3>
            <p>Known for its serene environment and comprehensive facilities.</p>
            <a href="select_options.php?hostel=KTR" class="btn-details" aria-label="View details about KTR hostel"><i class="fas fa-info-circle" aria-hidden="true"></i> View Details</a>
            <button class="btn-location" onclick="showMap('KTR')" aria-label="View map location for KTR"><i class="fas fa-map-marker-alt" aria-hidden="true"></i> View Location</button>
          </div>
        </article>

        <article class="hostel-card">
          <img src="images/ktf-exterior.jpg" alt="Exterior of Kolej Tun Fatimah">
          <div class="hostel-info">
            <h3>Kolej Tun Fatimah (KTF)</h3>
            <p>Modern and comfortable hostel with excellent amenities.</p>
            <a href="select_options.php?hostel=KTF" class="btn-details" aria-label="View details about KTF hostel"><i class="fas fa-info-circle" aria-hidden="true"></i> View Details</a>
            <button class="btn-location" onclick="showMap('KTF')" aria-label="View map location for KTF"><i class="fas fa-map-marker-alt" aria-hidden="true"></i> View Location</button>
          </div>
        </article>
      </div>

      <div id="map-section" class="map-section" aria-hidden="true">
        <div class="map-container">
          <iframe
            id="google-map"
            title="Hostel location map"
            src=""
            width="100%"
            height="400"
            style="border:0;"
            allowfullscreen=""
            loading="lazy">
          </iframe>
        </div>
        <button id="close-map" onclick="closeMap()" aria-label="Close map"><i class="fas fa-times" aria-hidden="true"></i> Close Map</button>
      </div>
    </section>
  </main>

  <!-- Chatbot Toggle -->
  <button id="chatbot-toggle" aria-label="Open chatbot"><i class="fas fa-comments" aria-hidden="true"></i></button>

  <!-- Chatbot Modal -->
  <div id="chatbot" role="dialog" aria-labelledby="chatbot-header" aria-hidden="true">
    <div id="chatbot-header">
      <span id="chatbot-header-label">UniStay Assistant</span>
      <button class="close-chatbot" aria-label="Close chatbot"><i class="fas fa-times" aria-hidden="true"></i></button>
    </div>
    <div id="chat-log" role="log" aria-live="polite">
      <p class="bot">Hello! Welcome to UniStay. May I have your name, please?</p>
    </div>
    <div id="chat-input">
      <label for="chat-message" class="visually-hidden">Type your message</label>
      <input type="text" id="chat-message" placeholder="Type your message..." aria-label="Type your message" />
      <button id="send-chat" aria-label="Send message"><i class="fas fa-paper-plane" aria-hidden="true"></i></button>
    </div>
  </div>

  <script>
document.addEventListener('DOMContentLoaded', function () {
    // Profile dropdown
    const profilePic = document.getElementById('profilePic');
    const dropdownMenu = document.getElementById('dropdownMenu');
    if (profilePic && dropdownMenu) {
        profilePic.addEventListener('click', function (e) {
            e.stopPropagation();
            dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
        });
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.profile-dropdown')) {
                dropdownMenu.style.display = 'none';
            }
        });
    }

  
    // Search functionality
    const searchInput = document.getElementById('search-bar');
    const searchButton = document.getElementById('search-button');
    const searchSuggestions = document.getElementById('search-suggestions');
    const suggestionList = document.getElementById('suggestion-list');
    const hostelGrid = document.getElementById('hostel-grid');
    const hostelCards = Array.from(document.querySelectorAll('.hostel-card'));

    const hostels = hostelCards.map(card => {
        return {
            name: card.querySelector('h3').textContent,
            element: card,
            id: card.querySelector('h3').textContent.replace(/\s+/g, '-').toLowerCase()
        };
    });

    hostels.forEach(hostel => {
        hostel.element.id = hostel.id;
    });

    searchInput.addEventListener('input', function () {
        const searchTerm = this.value.toLowerCase().trim();
        suggestionList.innerHTML = '';

        if (searchTerm.length > 0) {
            const matches = hostels.filter(hostel =>
                hostel.name.toLowerCase().includes(searchTerm)
            );

            if (matches.length > 0) {
                matches.forEach(hostel => {
                    const li = document.createElement('li');
                    li.textContent = hostel.name;
                    li.addEventListener('click', function () {
                        searchInput.value = hostel.name;
                        searchSuggestions.style.display = 'none';
                        scrollToHostel(hostel);
                    });
                    suggestionList.appendChild(li);
                });
                searchSuggestions.style.display = 'block';
            } else {
                const noResults = document.createElement('li');
                noResults.textContent = 'No hostels found';
                suggestionList.appendChild(noResults);
                searchSuggestions.style.display = 'block';
            }
        } else {
            searchSuggestions.style.display = 'none';
        }
    });

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.search-container')) {
            searchSuggestions.style.display = 'none';
        }
    });

    searchButton.addEventListener('click', function () {
        const searchTerm = searchInput.value.toLowerCase().trim();
        if (searchTerm) {
            const matchedHostel = hostels.find(hostel =>
                hostel.name.toLowerCase().includes(searchTerm)
            );
            if (matchedHostel) {
                scrollToHostel(matchedHostel);
            } else {
                hostelCards.forEach(card => card.style.display = 'none');
                const noResults = document.createElement('div');
                noResults.className = 'no-results';
                noResults.innerHTML = '<i class="fas fa-search" style="font-size: 2rem; margin-bottom: 10px;"></i><p>No hostels found matching your search</p>';
                hostelGrid.appendChild(noResults);
            }
        }
        searchSuggestions.style.display = 'none';
    });

    searchInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            searchButton.click();
        }
    });

    function scrollToHostel(hostel) {
        window.location.hash = hostel.id;
        hostel.element.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });

        hostel.element.style.boxShadow = '0 0 0 3px rgba(217, 35, 78, 0.5)';
        hostel.element.style.transition = 'box-shadow 0.3s ease';
        setTimeout(() => {
            hostel.element.style.boxShadow = '';
        }, 2000);

        hostelCards.forEach(card => card.style.display = 'block');

        const noResults = document.querySelector('.no-results');
        if (noResults) noResults.remove();
    }

   
  
});

// Attach map buttons
const mapButtons = document.querySelectorAll('.view-map-btn');
mapButtons.forEach(btn => {
    btn.addEventListener('click', function () {
        const hostelKey = this.getAttribute('data-hostel');
        if (hostelKey && hostelLocations[hostelKey]) {
            showMap(hostelKey);
        }
    });
});

const hostelLocations = {
  KDOJ: "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.308865378772!2d103.61841977581865!3d1.576103898409183!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31da7723b3292cdb%3A0x6ad2fa4652ebe37!2sKolej%20Dato%20Onn%20Jaafar!5e0!3m2!1sen!2smy!4v1750791886333!5m2!1sen!2smy",
  KTR: "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.3330831886146!2d103.62574836624295!3d1.5634084177614118!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31da77ca64f4abaf%3A0x5d44d2d69004f30a!2sK18%20Kolej%20Tun%20Razak!5e0!3m2!1sen!2smy!4v1750792047083!5m2!1sen!2smy",
  KTF: "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d997.0854086187206!2d103.62924839913514!3d1.55890096285075!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31da7786fffa2b73%3A0x8758abc7cc182958!2sH14%20KTF!5e0!3m2!1sen!2smy!4v1750792175758!5m2!1sen!2smy"
};

function showMap(hostel) {
  const mapFrame = document.getElementById('google-map');
  const mapSection = document.getElementById('map-section');
  const hostelGrid = document.getElementById('hostel-grid');

  // Update the iframe src with the correct map URL
  mapFrame.src = hostelLocations[hostel] || ""; // Use the corresponding URL or leave empty if not found

  // Trigger animations
  mapSection.classList.add('visible'); // Slide in map
  hostelGrid.classList.add('hidden'); // Dim hostel grid
}

function closeMap() {
  const mapFrame = document.getElementById('google-map');
  const mapSection = document.getElementById('map-section');
  const hostelGrid = document.getElementById('hostel-grid');

  // Clear iframe src to avoid unnecessary loading
  mapFrame.src = "";

  // Reverse animations
  mapSection.classList.remove('visible'); // Slide out map
  hostelGrid.classList.remove('hidden'); // Show hostel grid
}
   const chatbot = document.getElementById("chatbot");
    const toggleButton = document.getElementById("chatbot-toggle");
    const closeButton = document.querySelector(".close-chatbot");
    const chatLog = document.getElementById("chat-log");
    const chatMessage = document.getElementById("chat-message");

    let currentStep = 0;
    const steps = [
      "Hello! Welcome to UniStay. May I have your name, please?",
      "Nice to meet you! Which location are you looking for?",
      "Do you prefer a single or double bedroom?",
      ""
    ];

    // Open Chatbot
    toggleButton.addEventListener("click", () => {
      chatbot.style.display = "flex";
      toggleButton.style.display = "none";
    });

    // Close Chatbot
    closeButton.addEventListener("click", () => {
      chatbot.style.display = "none";
      toggleButton.style.display = "block";
    });

    // Chat Logic
    document.getElementById("send-chat").addEventListener("click", () => {
      const userInput = chatMessage.value.trim();
      if (userInput) {
        chatLog.innerHTML += `<p class="user">${userInput}</p>`;
        chatMessage.value = "";

        if (currentStep < steps.length - 1) {
          currentStep++;
          chatLog.innerHTML += `<p class="bot">${steps[currentStep]}</p>`;
        } else {
          chatLog.innerHTML += `<p class="bot">Thank you! Let me assist you with the best options based on your preferences.</p>`;
        }
        chatLog.scrollTop = chatLog.scrollHeight;
      }
    });
    
</script>

</body>
</html>