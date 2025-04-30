<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hotel_management";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}

// Function to check if experience is already booked for the date
function isExperienceBooked($conn, $experience, $date) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM experience_bookings WHERE experience = :experience AND booking_date = :date");
    $stmt->bindParam(':experience', $experience);
    $stmt->bindParam(':date', $date);
    $stmt->execute();
    return $stmt->fetchColumn() > 0;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $experience = $_POST['experience'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $guests = $_POST['guests'];
    $specialRequirements = $_POST['specialRequirements'];

    // Validate required fields
    if (empty($firstName) || empty($lastName) || empty($email) || empty($phone) || 
        empty($experience) || empty($date) || empty($time) || empty($guests)) {
        echo json_encode(['status' => 'error', 'message' => 'All required fields must be filled']);
        exit();
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
        exit();
    }

    // Validate date (must be future date)
    $bookingDate = new DateTime($date);
    $today = new DateTime();
    if ($bookingDate < $today) {
        echo json_encode(['status' => 'error', 'message' => 'Booking date must be in the future']);
        exit();
    }

    // Check if experience is already booked for the date
    if (isExperienceBooked($conn, $experience, $date)) {
        echo json_encode(['status' => 'error', 'message' => 'This experience is already booked for the selected date']);
        exit();
    }

    try {
        // Insert booking into database
        $stmt = $conn->prepare("INSERT INTO experience_bookings (
            first_name, last_name, email, phone, experience, 
            booking_date, booking_time, number_of_guests, special_requirements, 
            booking_status, created_at
        ) VALUES (
            :firstName, :lastName, :email, :phone, :experience,
            :date, :time, :guests, :specialRequirements,
            'pending', NOW()
        )");

        $stmt->bindParam(':firstName', $firstName);
        $stmt->bindParam(':lastName', $lastName);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':experience', $experience);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':time', $time);
        $stmt->bindParam(':guests', $guests);
        $stmt->bindParam(':specialRequirements', $specialRequirements);

        $stmt->execute();

       

        echo json_encode(['status' => 'success', 'message' => 'Booking successful! We will contact you shortly.']);
    } catch(PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?> 