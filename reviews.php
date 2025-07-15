<?php
session_start(); // Start the session at the very beginning

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html"); // Assuming your login page is index.html
    exit();
}

require 'db.php'; // Include your database connection file

$user_id = $_SESSION['user_id'];
$profile_pic_path = 'images/user.png'; // Default profile picture if none is found or set
$message = ''; // To display success or error messages

// --- Fetch User's Profile Picture for Navigation Bar ---
$stmt_profile_pic = $conn->prepare("SELECT profile_pic FROM users WHERE id = ?");
if ($stmt_profile_pic === false) {
    die("Error preparing statement for fetching profile pic: " . $conn->error);
}
$stmt_profile_pic->bind_param("i", $user_id);
$stmt_profile_pic->execute();
$result_profile_pic = $stmt_profile_pic->get_result();

if ($result_profile_pic->num_rows > 0) {
    $user_data = $result_profile_pic->fetch_assoc();
    if (!empty($user_data['profile_pic']) && file_exists('uploads/' . $user_data['profile_pic'])) {
        $profile_pic_path = 'uploads/' . htmlspecialchars($user_data['profile_pic']);
    }
}
$stmt_profile_pic->close();


// --- Handle Review Submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $hostel_name = trim($_POST['hostel']);
    $room_type = trim($_POST['room_type']);
    $review_text = trim($_POST['review_text']);

    // Basic validation
    if (empty($hostel_name) || empty($room_type) || empty($review_text)) {
        $message = "<p class='error-message'>Please fill in all review fields.</p>";
    } elseif (strlen($review_text) < 10) {
        $message = "<p class='error-message'>Review must be at least 10 characters long.</p>";
    } else {
        // Prepare and execute the INSERT statement
        $stmt_insert_review = $conn->prepare("INSERT INTO reviews (user_id, hostel_name, room_type, review_text) VALUES (?, ?, ?, ?)");
        if ($stmt_insert_review === false) {
            $message = "<p class='error-message'>Error preparing review submission: " . $conn->error . "</p>";
        } else {
            $stmt_insert_review->bind_param("isss", $user_id, $hostel_name, $room_type, $review_text);

            if ($stmt_insert_review->execute()) {
                $message = "<p class='success-message'>Thank you for your review!</p>";
                // After successful submission, redirect to avoid form resubmission on refresh
                // and to display the new review immediately.
                header("Location: reviews.php?success=1");
                exit();
            } else {
                $message = "<p class='error-message'>Error submitting review: " . $stmt_insert_review->error . "</p>";
            }
            $stmt_insert_review->close();
        }
    }
}

// Check for success message after redirect
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $message = "<p class='success-message'>Thank you for your review!</p>";
}


// --- Define "Bot" Reviews (Static Reviews) ---
$bot_reviews = [
    [
        'username' => 'Student Abdur',
        'hostel_name' => 'KDOJ',
        'room_type' => 'Single',
        'review_text' => 'Very clean and peaceful environment. Loved my stay!',
        'created_at' => '2023-01-15 10:00:00' // Example old date
    ],
    [
        'username' => 'Student omar ',
        'hostel_name' => 'KDOJ',
        'room_type' => 'Double',
        'review_text' => 'Great roommates and excellent facilities. Worth it!',
        'created_at' => '2023-02-20 11:30:00'
    ],
    [
        'username' => 'Student Ali',
        'hostel_name' => 'KTR',
        'room_type' => 'Single',
        'review_text' => 'Perfect for privacy. Highly recommend!',
        'created_at' => '2023-03-01 09:15:00'
    ],
    [
        'username' => 'Student fatima',
        'hostel_name' => 'KTF',
        'room_type' => 'Double',
        'review_text' => 'Comfortable and affordable. Would book again.',
        'created_at' => '2023-04-05 14:00:00'
    ],
    [
        'username' => 'Student Hossain',
        'hostel_name' => 'KDOJ',
        'room_type' => 'Double',
        'review_text' => 'I made a lifelong friend! The room was spacious enough for two and we split chores fairly.',
        'created_at' => '2023-05-10 16:45:00'
    ],
    [
        'username' => 'Student Khadija',
        'hostel_name' => 'KTF',
        'room_type' => 'Single',
        'review_text' => 'Budget-friendly and you never feel lonely. Perfect for Girls.',
        'created_at' => '2023-06-18 08:00:00'
    ],
    [
        'username' => 'Student Tanisha ',
        'hostel_name' => 'KTF',
        'room_type' => 'Single',
        'review_text' => 'I’m not very social, so having a single room helped me feel comfortable and focused.',
        'created_at' => '2023-07-22 13:00:00'
    ],
    [
        'username' => 'Student Akib',
        'hostel_name' => 'KTR',
        'room_type' => 'Double',
        'review_text' => 'I was worried about space, but we managed perfectly fine. It’s cozy and a great way to save money.',
        'created_at' => '2023-08-01 10:00:00'
    ]
];


// --- Fetch Existing Reviews from Database ---
$db_reviews = []; // Array to store fetched reviews
$stmt_fetch_reviews = $conn->prepare("
    SELECT r.review_text, r.hostel_name, r.room_type, r.created_at, u.username
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    ORDER BY r.created_at DESC
");
if ($stmt_fetch_reviews === false) {
    die("Error preparing statement for fetching reviews: " . $conn->error);
}
$stmt_fetch_reviews->execute();
$result_reviews = $stmt_fetch_reviews->get_result();

while ($row = $result_reviews->fetch_assoc()) {
    $db_reviews[] = $row;
}
$stmt_fetch_reviews->close();


// --- Combine and Sort All Reviews ---
// Combine database reviews and bot reviews
$all_reviews = array_merge($db_reviews, $bot_reviews);

// Sort all reviews by creation date (newest first)
usort($all_reviews, function($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});


$conn->close(); // Close the database connection after all operations
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Student Reviews - UniStay</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
      /* Universal Resets */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Base Body Styles */
body {
    font-family: 'Segoe UI', sans-serif;
    color: #333;
    background: #f9f9f9; /* Default background, can be overridden by specific pages */
}

/* --- UniStay Navigation Bar Styles --- */
nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #d9234e; /* UniStay red/pink color */
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
    color: white;
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
    transition: all 0.3s;
}

.nav-right a:hover {
    text-decoration: underline;
    opacity: 0.9;
}

/* Profile Dropdown Styles */
.profile-dropdown {
    position: relative;
}

.profile-pic {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    cursor: pointer;
    border: 2px solid #fff;
    object-fit: cover;
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
    transition: background-color 0.3s;
}

.dropdown-content a:hover {
    background-color: #f2f2f2;
}

.profile-dropdown:hover .dropdown-content {
    display: block;
}

/* --- Dashboard Specific Styles --- */
.hero-section {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 40px 20px;
    background: #fff;
    flex-wrap: wrap;
}

.booking-card {
    background: white;
    padding: 24px 20px;
    border-radius: 16px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    max-width: 360px;
    width: 100%;
    margin: 20px;
}

.booking-card h2 {
    font-size: 22px;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 15px;
}

label {
    display: block;
    font-weight: 600;
    margin-bottom: 6px;
}

input[type="text"],
input[type="email"],
input[type="file"],
textarea, /* Added textarea to general input styles */
select {
    width: 100%;
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 15px;
}

button {
    width: 100%;
    padding: 12px;
    background-color: #ff385c;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 15px;
    cursor: pointer;
    margin-top: 10px;
    transition: all 0.3s;
}

button:hover {
    background-color: #e03150;
}

.image-preview {
    max-width: 440px;
    border-radius: 16px;
    overflow: hidden;
    margin: 20px;
}

.image-preview img {
    width: 100%;
    height: auto;
    border-radius: 16px;
}

.features {
    display: flex;
    justify-content: space-around;
    padding: 40px 10px;
    background: #fff;
    flex-wrap: wrap;
    text-align: center;
}

.feature {
    flex: 1;
    min-width: 220px;
    max-width: 300px;
    padding: 10px;
}

.feature h3 {
    margin-bottom: 10px;
    color: #d9234e;
    font-size: 18px;
}

.feature p {
    color: #555;
    font-size: 14px;
}

/* --- Profile Page Specific Styles --- */
.profile-container {
    max-width: 500px;
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin: 40px auto;
}

.update-profile-title { /* For update_profile.php h2 */
    color: #333;
    text-align: center;
    margin-bottom: 25px;
}

.profile-form label {
    display: block;
    margin: 15px 0 5px;
}

.profile-form input[type="text"],
.profile-form input[type="email"],
.profile-form input[type="file"] {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 6px;
}

.current-profile-pic { /* For the profile picture shown on profile.php and update_profile.php */
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 10px;
    display: block;
    margin-left: auto;
    margin-right: auto;
}

.profile-form button {
    margin-top: 20px;
    background-color: #2a7ae2; /* Blue button for profile update */
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.profile-form button:hover {
    background-color: #1d5ec9;
}

.success-message { /* Unified success message style */
    color: green;
    font-weight: bold;
    text-align: center;
    margin-bottom: 20px;
}
.error-message { /* Unified error message style */
    color: red;
    font-weight: bold;
    text-align: center;
    margin-bottom: 20px;
}


/* --- Reviews Page Specific Styles --- */
main {
    max-width: 1000px;
    margin: 40px auto;
    padding: 0 20px;
    background: #fff; /* Added background for the main content area */
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.05);
}

.reviews-heading { /* New class for the H2 in reviews page */
    text-align: center;
    margin-bottom: 40px;
    font-size: 28px;
    color: rgb(104, 104, 105);
    padding-top: 20px; /* Add padding at top */
}

.review-submission-section {
    background: #fdfdfd;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    margin-bottom: 40px;
}

.review-submission-section h3 {
    text-align: center;
    margin-bottom: 25px;
    color: #d9234e; /* UniStay red for form title */
    font-size: 24px;
}

.review-submission-section .form-group {
    margin-bottom: 20px;
}

.review-submission-section label {
    font-weight: 600;
    margin-bottom: 8px;
    color: #555;
}

.review-submission-section textarea {
    min-height: 100px; /* Make textarea larger */
    resize: vertical; /* Allow vertical resizing */
}

.review-submission-section button {
    background-color: #2a7ae2; /* Blue button for submission */
    transition: background-color 0.3s;
}

.review-submission-section button:hover {
    background-color: #1d5ec9;
}


.review-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
    padding-bottom: 30px; /* Padding at bottom of grid */
}

.review-card {
    background: white;
    border-radius: 16px;
    padding: 24px 20px;
    box-shadow: 0 10px 24px rgba(0, 0, 0, 0.08);
    position: relative;
    border: 1px solid #eee; /* Subtle border */
}

.review-card::before {
    content: "“";
    font-size: 60px;
    color: #eee;
    position: absolute;
    top: -20px;
    left: 20px;
    font-family: serif;
    z-index: 0; /* Ensure it stays behind text */
}

.review-card h3 {
    margin-top: 0;
    color: #333; /* Darker color for headings */
    font-size: 18px;
    margin-bottom: 5px; /* Less space to next line */
    position: relative; /* Bring heading above pseud-element */
    z-index: 1;
}
.review-card .reviewer-info { /* New style for reviewer name/date */
    font-size: 0.9em;
    color: #777;
    margin-bottom: 15px;
    position: relative;
    z-index: 1;
}

.review-card p {
    font-size: 15px;
    color: #444;
    line-height: 1.6;
    position: relative;
    z-index: 1;
}

/* --- Media Queries (for responsiveness) --- */
@media (max-width: 600px) {
    nav {
        flex-direction: column;
        align-items: flex-start;
        padding: 10px 15px;
    }

    .logo {
        margin-bottom: 10px;
    }

    .nav-right {
        margin-top: 0;
        width: 100%;
        justify-content: space-around;
        gap: 10px;
    }
    .nav-right a {
        font-size: 0.9rem;
    }
    .profile-dropdown {
        flex-grow: 1; /* Allow dropdown to take available space */
        text-align: right; /* Align profile pic to right within its flexible space */
    }
    .dropdown-content {
        right: 0;
        left: auto; /* Ensure it sticks to the right edge */
    }

    main {
        margin: 20px 10px;
        padding: 0 10px;
    }

    .reviews-heading {
        font-size: 24px;
        margin-bottom: 25px;
    }

    .review-submission-section {
        padding: 20px;
        margin-bottom: 30px;
    }

    .review-submission-section h3 {
        font-size: 20px;
        margin-bottom: 20px;
    }

    .review-grid {
        gap: 15px;
    }

    .review-card::before {
        top: -10px;
        left: 10px;
        font-size: 40px;
    }
}

@media (max-width: 768px) {
    .hero-section {
        flex-direction: column;
        padding: 20px 10px;
    }

    .booking-card,
    .image-preview {
        margin: 10px 0;
    }
}
    </style>
    </head>
<body>

    <nav>
        <div class="logo"><b>UniStay</b></div>
        <div class="nav-right">
            <a href="dashboard.php">Home</a>
            <a href="reviews.php">Review</a>
            <a href="gallery.php">Gallery</a>

            <div class="profile-dropdown" id="profileDropdown">
                <img src="<?= $profile_pic_path ?>" alt="Profile" class="profile-pic" id="profilePic" />
                <div class="dropdown-content" id="dropdownMenu">
                    <a href="profile.php">Profile</a>
                    <a href="booking_history.php">Booking History</a>
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        </div>
    </nav>
    <main>
        <h2 class="reviews-heading">Student Reviews</h2>

        <?php if (!empty($message)): ?>
            <?= $message ?>
        <?php endif; ?>

        <section class="review-submission-section">
            <h3>Leave Your Review</h3>
            <form action="reviews.php" method="POST">
                <div class="form-group">
                    <label for="hostel">Hostel Name</label>
                    <select name="hostel" id="hostel" required>
                        <option value="">Select Hostel</option>
                        <option value="KDOJ">Kolej Dato' Onn Jaafar (KDOJ)</option>
                        <option value="KTR">Kolej Tun Razak (KTR)</option>
                        <option value="KTF">Kolej Tun Fatimah (KTF)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="room_type">Room Type</label>
                    <select name="room_type" id="room_type" required>
                        <option value="">Select Room Type</option>
                        <option value="Single">Single Bed Room</option>
                        <option value="Double">Double Bed Room</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="review_text">Your Review</label>
                    <textarea name="review_text" id="review_text" rows="5" placeholder="Share your experience here..." required></textarea>
                </div>
                <button type="submit" name="submit_review">Submit Review</button>
            </form>
        </section>

        <div class="review-grid">
            <?php if (empty($all_reviews)): ?>
                <p style="text-align: center; grid-column: 1 / -1; color: #777;">No reviews yet. Be the first to leave one!</p>
            <?php else: ?>
                <?php foreach ($all_reviews as $review): ?>
                    <div class="review-card">
                        <h3><?= htmlspecialchars($review['hostel_name']) ?> - <?= htmlspecialchars($review['room_type']) ?></h3>
                        <div class="reviewer-info">
                            By <?= htmlspecialchars($review['username']) ?> on <?= date('M d, Y', strtotime($review['created_at'])) ?>
                        </div>
                        <p><?= nl2br(htmlspecialchars($review['review_text'])) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const profilePic = document.getElementById('profilePic');
            const dropdownMenu = document.getElementById('dropdownMenu');

            // Toggle dropdown visibility when clicking on profile picture
            profilePic.addEventListener('click', function (e) {
                e.stopPropagation(); // Prevent document click from closing it immediately
                dropdownMenu.style.display =
                    dropdownMenu.style.display === 'block' ? 'none' : 'block';
            });

            // Close dropdown when clicking anywhere outside the profile dropdown area
            document.addEventListener('click', function (e) {
                if (!e.target.closest('.profile-dropdown')) {
                    dropdownMenu.style.display = 'none';
                }
            });

            // Ensure dropdown links navigate properly
            document.querySelectorAll('.dropdown-content a').forEach((link) => {
                link.addEventListener('click', function () {
                    // No special action required, browser handles the navigation
                });
            });
        });
    </script>

</body>
</html>