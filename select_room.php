<?php
session_start();

// Define valid options for safety
$valid_hostels = ['KDOJ', 'KTR', 'KTF'];
$valid_rooms = ['Single', 'Double'];
$valid_durations = ['1 Semester', '2 Semesters'];

// Validate input
if (
    !isset($_POST['hostel'], $_POST['room'], $_POST['duration']) ||
    !in_array($_POST['hostel'], $valid_hostels) ||
    !in_array($_POST['room'], $valid_rooms) ||
    !in_array($_POST['duration'], $valid_durations)
) {
    die("Invalid booking selection.");
}

// Store booking in session
$_SESSION['booking'] = [
    'hostel' => $_POST['hostel'],
    'room' => $_POST['room'],
    'duration' => $_POST['duration'] === '2 Semesters' ? 2 : 1
];

// Redirect to room details
header('Location: room_details.php');
exit();
?>
