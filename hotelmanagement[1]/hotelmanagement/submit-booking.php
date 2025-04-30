<?php

$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "hotel_management";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $location = htmlspecialchars($_POST['location']);
    $checkin_date = htmlspecialchars($_POST['checkin_date']);
    $checkout_date = htmlspecialchars($_POST['checkout_date']);
    $rooms = htmlspecialchars($_POST['rooms']);
    $guests = htmlspecialchars($_POST['guests']);
    $room_type = htmlspecialchars($_POST['room_type']);

    // Validate form data
    if (empty($location) || empty($checkin_date) || empty($checkout_date) || empty($rooms) || empty($guests) || empty($room_type)) {
        echo "All fields are required.";
        exit;
    }

    // Check for duplicate bookings on the same date
    $check_query = $conn->prepare("SELECT * FROM bookings WHERE location = ? AND checkin_date = ? AND checkout_date = ? AND room_type = ?");
    $check_query->bind_param("ssss", $location, $checkin_date, $checkout_date, $room_type);
    $check_query->execute();
    $result = $check_query->get_result();

    if ($result->num_rows > 0) {
        echo "<h1>Room Availability Status</h1>";
        echo "<p style='color: red;'>Not Available: A booking already exists for the selected dates, location, and room type.</p>";
        exit;
    }

    // Check if the number of bookings for the selected room type exceeds 3
    $room_count_query = $conn->prepare("SELECT SUM(rooms) AS total_rooms FROM bookings WHERE room_type = ? AND checkin_date = ? AND checkout_date = ?");
    $room_count_query->bind_param("sss", $room_type, $checkin_date, $checkout_date);
    $room_count_query->execute();
    $room_count_result = $room_count_query->get_result();
    $room_count = $room_count_result->fetch_assoc()['total_rooms'];

    if ($room_count + $rooms > 3) {
        echo "<h1>Room Availability Status</h1>";
        echo "<p style='color: red;'>Not Available: Only " . (3 - $room_count) . " rooms are available for the selected room type on the chosen dates.</p>";
        exit;
    }

    // If we reach here, the room is available
    echo "<h1>Room Availability Status</h1>";
    echo "<p style='color: green;'>Available: The requested rooms are available for booking.</p>";
    echo "<p><strong>Location:</strong> $location</p>";
    echo "<p><strong>Check-In Date:</strong> $checkin_date</p>";
    echo "<p><strong>Check-Out Date:</strong> $checkout_date</p>";
    echo "<p><strong>Rooms:</strong> $rooms</p>";
    echo "<p><strong>Guests:</strong> $guests</p>";
    echo "<p><strong>Room Type:</strong> $room_type</p>";
    echo"<a href='roombooking.html'><button>Book Now</button></a>";

    $check_query->close();
    $room_count_query->close();
} else {
    // Redirect to the booking form if accessed directly
    header("Location: index.html");
    exit;
}

$conn->close();
?>