<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

require 'db.php'; // Your DB connection file

$user_id = $_SESSION['user_id'];
$profile_pic_path = 'images/user.png'; // Default path for nav bar profile pic

// Fetch user data for the navigation bar profile picture
$stmt = $conn->prepare("SELECT profile_pic FROM users WHERE id = ?");
if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_profile_pic = $stmt->get_result();
$user_profile_data = $result_profile_pic->fetch_assoc();

// Set the profile picture path for the navigation bar
if (!empty($user_profile_data['profile_pic']) && file_exists('uploads/' . $user_profile_data['profile_pic'])) {
    $profile_pic_path = 'uploads/' . htmlspecialchars($user_profile_data['profile_pic']);
} else {
    $profile_pic_path = 'images/user.png'; // Ensure it's the default if null or file missing
}
$stmt->close();


// Fetch booking history data
$query = $conn->prepare("
    SELECT hostel, room_type, duration, booking_date 
    FROM bookings 
    WHERE user_id = ?
    ORDER BY booking_date DESC
");
$query->bind_param("i", $user_id);
$query->execute();
$result_bookings = $query->get_result();

$conn->close(); // Close the connection after fetching all data
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Booking History - UniStay</title>
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
            background-color: #f7f7f7; /* Specific background for booking history page */
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

        /* --- Booking History Specific Styles --- */
        .booking-history-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .booking-history-container h2 {
            color: #333;
            text-align: center;
            margin-bottom: 25px;
            font-size: 2rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 14px 18px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #e9ecef;
            color: #555;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 0.9em;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .no-history {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #777;
        }

        /* Media Queries (for responsiveness) */
        @media (max-width: 768px) {
            .booking-history-container {
                margin: 20px 15px;
                padding: 20px;
            }
            table, thead, tbody, th, td, tr {
                display: block;
            }
            thead tr {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }
            tr {
                border: 1px solid #ccc;
                margin-bottom: 15px;
                border-radius: 8px;
                overflow: hidden;
            }
            td {
                border: none;
                border-bottom: 1px solid #eee;
                position: relative;
                padding-left: 50%;
                text-align: right;
            }
            td:before {
                position: absolute;
                top: 0;
                left: 6px;
                width: 45%;
                padding-right: 10px;
                white-space: nowrap;
                text-align: left;
                font-weight: bold;
                color: #555;
            }
            td:nth-of-type(1):before { content: "Hostel:"; }
            td:nth-of-type(2):before { content: "Room Type:"; }
            td:nth-of-type(3):before { content: "Duration:"; }
            td:nth-of-type(4):before { content: "Booking Date:"; }
        }

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
            .booking-history-container {
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

    <main class="booking-history-container">
        <h2>My Booking History</h2>
        
        <?php if ($result_bookings->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Hostel</th>
                        <th>Room Type</th>
                        <th>Duration</th>
                        <th>Booking Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result_bookings->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['hostel']) ?></td>
                        <td><?= htmlspecialchars($row['room_type']) ?></td>
                        <td>
                            <?php
                                $d = $row['duration'];
                                echo ($d == "1") ? "1 semester" : (($d == "2") ? "2 semesters" : htmlspecialchars($d));
                            ?>
                        </td>
                        <td><?= htmlspecialchars($row['booking_date']) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-history">
                <p>No booking history found.</p>
            </div>
        <?php endif; ?>
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