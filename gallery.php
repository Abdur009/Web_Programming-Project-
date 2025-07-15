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

// Fetch user data, including profile picture
$stmt = $conn->prepare("SELECT profile_pic FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    // Check if profile_pic is not empty and the file actually exists
    if (!empty($user['profile_pic']) && file_exists('uploads/' . $user['profile_pic'])) {
        $profile_pic_path = 'uploads/' . htmlspecialchars($user['profile_pic']);
    }
}
$stmt->close();
$conn->close(); // Close the connection after fetching data
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery - UniStay</title>
    <style>
        /* Universal Resets */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Base Body Styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f6f9;
        }

        /* --- UniStay Navigation Bar Styles (Common) --- */
        nav {
            background:#d9234e;
            color: white;
            padding: 12px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .logo {
            font-size: 1.8rem; /* Consistent with other pages */
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

        /* --- Gallery Specific Styles --- */
        main {
            padding: 50px 20px;
            max-width: 1200px;
            margin: auto;
        }

        h2 {
            text-align: center;
            margin-bottom: 40px;
            font-size: 28px;
            color:rgb(116, 112, 116);
        }

        .gallery {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
        }

        .gallery-item {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            transition: transform 0.2s ease;
            cursor: pointer; /* Indicate it's clickable */
        }

        .gallery-item:hover {
            transform: scale(1.02);
        }

        .gallery-item img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            display: block; /* Remove extra space below image */
        }

        .caption {
            padding: 12px 15px;
            text-align: center;
            font-weight: bold;
            background-color: #f8f9fa;
            color: #333;
        }

        /* --- Image Preview Modal Styles --- */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 2000; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgba(0,0,0,0.8); /* Black w/ opacity */
            justify-content: center; /* Center horizontally */
            align-items: center; /* Center vertically */
        }

        .modal-content {
            margin: auto;
            display: block;
            max-width: 90%;
            max-height: 90%;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.5);
        }

        #caption-modal {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 700px;
            text-align: center;
            color: #ccc;
            padding: 10px 0;
            height: 150px; /* Space for caption */
        }

        .close-button {
            position: absolute;
            top: 20px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            transition: 0.3s;
            cursor: pointer;
        }

        .close-button:hover,
        .close-button:focus {
            color: #bbb;
            text-decoration: none;
            cursor: pointer;
        }

        /* Media Queries */
        @media (max-width: 600px) {
            main {
                padding: 30px 10px;
            }
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
            .close-button {
                top: 15px;
                right: 20px;
                font-size: 30px;
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
        <h2>Hostel Room Gallery</h2>
        <div class="gallery">
            <div class="gallery-item">
                <img src="images/kdoj_single.jpg" alt="KDOJ Single">
                <div class="caption">KDOJ - Single Room</div>
            </div>
            <div class="gallery-item">
                <img src="images/kdoj_double.jpg" alt="KDOJ Double">
                <div class="caption">KDOJ - Double Room</div>
            </div>
            <div class="gallery-item">
                <img src="images/ktr_single.jpg" alt="KTR Single">
                <div class="caption">KTR - Single Room</div>
            </div>
            <div class="gallery-item">
                <img src="images/ktr_double.jpg" alt="KTR Double">
                <div class="caption">KTR - Double Room</div>
            </div>
            <div class="gallery-item">
                <img src="images/ktf_single.jpg" alt="KTF Single">
                <div class="caption">KTF - Single Room</div>
            </div>
            <div class="gallery-item">
                <img src="images/ktf_double.jpg" alt="KTF Double">
                <div class="caption">KTF - Double Room</div>
            </div>
        </div>
    </main>

    <div id="imageModal" class="modal">
        <span class="close-button">&times;</span>
        <img class="modal-content" id="imgPreview">
        <div id="caption-modal"></div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Navigation Bar Dropdown Logic
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

            // Image Gallery Preview Modal Logic
            const modal = document.getElementById("imageModal");
            const modalImg = document.getElementById("imgPreview");
            const modalCaption = document.getElementById("caption-modal");
            const galleryItems = document.querySelectorAll(".gallery-item img"); // Select image elements
            const closeButton = document.querySelector(".close-button");

            galleryItems.forEach(item => {
                item.addEventListener("click", function() {
                    modal.style.display = "flex"; // Use flex to center
                    modalImg.src = this.src;
                    modalCaption.innerHTML = this.nextElementSibling.innerHTML; // Get caption from sibling div
                });
            });

            // Close the modal when clicking on the close button
            closeButton.addEventListener("click", function() {
                modal.style.display = "none";
            });

            // Close the modal when clicking outside the image (on the overlay)
            modal.addEventListener("click", function(event) {
                if (event.target === modal) {
                    modal.style.display = "none";
                }
            });
        });
    </script>
</body>
</html>