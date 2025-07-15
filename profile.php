<?php
session_start(); // Start the session at the very beginning
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

require 'db.php'; // Include your database connection file

$user_id = $_SESSION['user_id'];
$profile_pic_path = 'images/user.png'; // Default path for nav bar profile pic

// Fetch user data for both the main profile content and the navigation bar
$stmt = $conn->prepare("SELECT username, email, phone_number, profile_pic FROM users WHERE id = ?");
if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("❌ Error: User not found.");
}

$user = $result->fetch_assoc();

// Set the profile picture path for the navigation bar and main display
if (!empty($user['profile_pic']) && file_exists('uploads/' . $user['profile_pic'])) {
    $profile_pic_path = 'uploads/' . htmlspecialchars($user['profile_pic']);
    $current_profile_pic_exists = true; // Flag to show delete button
} else {
    $profile_pic_path = 'images/user.png'; // Ensure it's the default if null or file missing
    $current_profile_pic_exists = false;
}

$conn->close(); // Close the connection after fetching data
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - UniStay</title>
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
            background-color: #f7f7f7; /* Specific background for profile page */
        }

        /* --- UniStay Navigation Bar Styles (Common) --- */
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

        /* --- Profile Page Specific Styles --- */
        .profile-container {
            max-width: 500px;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin: 40px auto;
        }

        .profile-container h2 {
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

        .current-profile-pic {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .profile-actions {
            display: flex;
            flex-direction: column; /* Stack buttons vertically */
            gap: 10px; /* Space between buttons */
            margin-top: 20px; /* Space from the input fields */
        }

        .profile-form button { /* General button style for profile form */
            width: 100%; /* Make buttons full width */
            padding: 12px;
            background-color: #2a7ae2; /* Blue button for profile update */
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 0; /* Override default button margin-top */
        }

        .profile-form button:hover {
            background-color: #1d5ec9;
        }

        .profile-form button.delete-profile-pic-btn {
            background-color: #dc3545; /* Red color for delete action */
        }

        .profile-form button.delete-profile-pic-btn:hover {
            background-color: #c82333;
        }

        /* Message styles */
        .success-message {
            color: green;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }
        .error-message {
            color: red;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }

        /* Media Queries (for responsiveness) */
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
                flex-grow: 1;
                text-align: right;
            }
            .dropdown-content {
                right: 0;
                left: auto;
            }
            .profile-container {
                margin: 20px 15px;
                padding: 20px;
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

    <main class="profile-container">
        <?php if (isset($_GET['updated'])): ?>
            <p class="success-message">✅ Profile updated successfully!</p>
        <?php endif; ?>
        <?php if (isset($_GET['deleted'])): ?>
            <p class="success-message">✅ Profile picture deleted successfully!</p>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <p class="error-message">❌ Error: <?= htmlspecialchars($_GET['error']) ?></p>
        <?php endif; ?>

        <h2>My Profile</h2>

        <form class="profile-form" method="POST" action="update_profile.php" enctype="multipart/form-data">
            <img class="current-profile-pic" src="<?= $profile_pic_path ?>" alt="Profile Picture">

            <label for="username">Username</label>
            <input type="text" id="username" value="<?= htmlspecialchars($user['username']) ?>" readonly>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

            <label for="phone_number">Phone Number</label>
            <input type="text" id="phone_number" name="phone_number" value="<?= htmlspecialchars($user['phone_number']) ?>" required>

            <label for="profile_pic">Change Profile Picture</label>
            <input type="file" id="profile_pic" name="profile_pic" accept="image/jpeg, image/png, image/gif, image/webp">

            <div class="profile-actions">
                <button type="submit">Update Profile</button>

                <?php if ($current_profile_pic_exists): ?>
                    <button type="submit" name="delete_profile_pic" class="delete-profile-pic-btn" onclick="return confirm('Are you sure you want to delete your profile picture?');">
                        Delete Profile Picture
                    </button>
                <?php endif; ?>
            </div>
        </form>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const profilePic = document.getElementById('profilePic');
            const dropdownMenu = document.getElementById('dropdownMenu');

            profilePic.addEventListener('click', function (e) {
                e.stopPropagation();
                dropdownMenu.style.display =
                    dropdownMenu.style.display === 'block' ? 'none' : 'block';
            });

            document.addEventListener('click', function (e) {
                if (!e.target.closest('.profile-dropdown')) {
                    dropdownMenu.style.display = 'none';
                }
            });

            document.querySelectorAll('.dropdown-content a').forEach((link) => {
                link.addEventListener('click', function () {
                    // No special action required, browser handles the navigation
                });
            });
        });
    </script>
</body>
</html>