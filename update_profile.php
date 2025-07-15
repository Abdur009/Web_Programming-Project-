<?php
session_start();
require 'db.php'; // Your database connection

if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$upload_dir = 'uploads/';
$message = ''; // For error/success messages
$redirect_url = 'profile.php'; // Default redirect


// --- Handle Profile Picture Deletion ---
if (isset($_POST['delete_profile_pic'])) {
    // Fetch current profile pic path from DB
    $stmt = $conn->prepare("SELECT profile_pic FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $old_profile_pic = $user['profile_pic'];
    $stmt->close();

    // Delete file from server if it exists and is not the default
    if (!empty($old_profile_pic) && file_exists($upload_dir . $old_profile_pic)) {
        if (!unlink($upload_dir . $old_profile_pic)) {
            $message = "Failed to delete old profile picture file.";
            header("Location: {$redirect_url}?error=" . urlencode($message));
            exit();
        }
    }

    // Update database to NULL
    $stmt = $conn->prepare("UPDATE users SET profile_pic = NULL WHERE id = ?");
    if ($stmt === false) {
        $message = "Error preparing DB update: " . $conn->error;
        header("Location: {$redirect_url}?error=" . urlencode($message));
        exit();
    }
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        header("Location: {$redirect_url}?deleted=1");
        exit();
    } else {
        $message = "Error updating database: " . $stmt->error;
        header("Location: {$redirect_url}?error=" . urlencode($message));
        exit();
    }
    $stmt->close();
    exit(); // Exit after deletion
}


// --- Handle Profile Update (Email, Phone, New Profile Picture) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);
    $new_profile_pic_name = NULL; // Default to NULL if no new pic or existing pic
    $current_profile_pic_db = NULL; // To store current DB profile pic


    // First, get the current profile picture path from the database
    $stmt = $conn->prepare("SELECT profile_pic FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
        $current_profile_pic_db = $user_data['profile_pic'];
    }
    $stmt->close();

    // Handle new profile picture upload
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $file_tmp_name = $_FILES['profile_pic']['tmp_name'];
        $file_name = $_FILES['profile_pic']['name'];
        $file_size = $_FILES['profile_pic']['size'];
        $file_type = $_FILES['profile_pic']['type'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allowed_extensions = array("jpeg", "jpg", "png", "gif", "webp");

        if (in_array($file_ext, $allowed_extensions)) {
            // Generate a unique file name
            $new_profile_pic_name = uniqid('profile_', true) . '.' . $file_ext;
            $upload_path = $upload_dir . $new_profile_pic_name;

            // Delete old profile picture file if it exists and is not the default
            if (!empty($current_profile_pic_db) && file_exists($upload_dir . $current_profile_pic_db)) {
                if (!unlink($upload_dir . $current_profile_pic_db)) {
                    $message = "Failed to delete old profile picture file.";
                    header("Location: {$redirect_url}?error=" . urlencode($message));
                    exit();
                }
            }

            // Move the new uploaded file
            if (!move_uploaded_file($file_tmp_name, $upload_path)) {
                $message = "Failed to upload new profile picture.";
                header("Location: {$redirect_url}?error=" . urlencode($message));
                exit();
            }
        } else {
            $message = "Error: Only JPG, JPEG, PNG, GIF, WEBP files are allowed for profile picture.";
            header("Location: {$redirect_url}?error=" . urlencode($message));
            exit();
        }
    } else if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] !== UPLOAD_ERR_NO_FILE) {
        // Handle other upload errors besides no file selected
        $message = "Profile picture upload error: " . $_FILES['profile_pic']['error'];
        header("Location: {$redirect_url}?error=" . urlencode($message));
        exit();
    } else {
        // No new file uploaded, keep the existing one in the DB (if any)
        $new_profile_pic_name = $current_profile_pic_db;
    }


    // Update user data in the database
    if (empty($email) || empty($phone_number)) {
        $message = "Email and Phone Number cannot be empty.";
        header("Location: {$redirect_url}?error=" . urlencode($message));
        exit();
    }

    $stmt = $conn->prepare("UPDATE users SET email = ?, phone_number = ?, profile_pic = ? WHERE id = ?");
    if ($stmt === false) {
        $message = "Error preparing DB update: " . $conn->error;
        header("Location: {$redirect_url}?error=" . urlencode($message));
        exit();
    }

    $stmt->bind_param("sssi", $email, $phone_number, $new_profile_pic_name, $user_id);

    if ($stmt->execute()) {
        header("Location: {$redirect_url}?updated=1");
        exit();
    } else {
        $message = "Error updating profile: " . $stmt->error;
        header("Location: {$redirect_url}?error=" . urlencode($message));
        exit();
    }

    $stmt->close();
}

$conn->close();
?>


