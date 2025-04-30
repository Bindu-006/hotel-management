<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hotel_management";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed. Please try again later.']);
    exit();
}

// Function to sanitize input data
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to generate booking ID
function generate_booking_id() {
    return 'RPP-' . strtoupper(substr(uniqid(), -5));
}

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Log received data for debugging
        error_log("Received POST data: " . print_r($_POST, true));

        // Sanitize and validate required fields
        $firstName = sanitize_input($_POST['firstName']);
        $lastName = sanitize_input($_POST['lastName']);
        $email = filter_var(sanitize_input($_POST['email']), FILTER_VALIDATE_EMAIL);
        $phone = sanitize_input($_POST['phone']);
        $country = sanitize_input($_POST['country']);
        $checkInDate = sanitize_input($_POST['checkInDate']);
        $checkOutDate = sanitize_input($_POST['checkOutDate']);
        $adults = (int)$_POST['adults'];
        $children = (int)$_POST['children'];
        $offerSelection = sanitize_input($_POST['offerSelection']);
        $paymentMethod = sanitize_input($_POST['paymentMethod']);
        
        // Validate required fields
        if (!$firstName || !$lastName || !$email || !$phone || !$country || !$checkInDate || !$checkOutDate) {
            throw new Exception("All required fields must be filled out");
        }

        // Validate email
        if (!$email) {
            throw new Exception("Invalid email format");
        }

        // Validate dates
        $checkIn = new DateTime($checkInDate);
        $checkOut = new DateTime($checkOutDate);
        $today = new DateTime();

        if ($checkIn < $today) {
            throw new Exception("Check-in date cannot be in the past");
        }

        if ($checkOut <= $checkIn) {
            throw new Exception("Check-out date must be after check-in date");
        }

        // Generate booking ID
        $bookingId = generate_booking_id();

        // Prepare SQL statement
        $sql = "INSERT INTO offers (
            booking_id, first_name, last_name, email, phone, country,
            check_in_date, check_out_date, adults, children,
            offer_selection, payment_method, booking_date
        ) VALUES (
            :booking_id, :first_name, :last_name, :email, :phone, :country,
            :check_in_date, :check_out_date, :adults, :children,
            :offer_selection, :payment_method, NOW()
        )";

        $stmt = $conn->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':booking_id', $bookingId);
        $stmt->bindParam(':first_name', $firstName);
        $stmt->bindParam(':last_name', $lastName);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':country', $country);
        $stmt->bindParam(':check_in_date', $checkInDate);
        $stmt->bindParam(':check_out_date', $checkOutDate);
        $stmt->bindParam(':adults', $adults);
        $stmt->bindParam(':children', $children);
        $stmt->bindParam(':offer_selection', $offerSelection);
        $stmt->bindParam(':payment_method', $paymentMethod);

        // Execute the statement
        $stmt->execute();
       
        // Return success response
       echo"<h1>Booking confirmed successfully</h1>";

    } catch (Exception $e) {
        error_log("Booking error: " . $e->getMessage());
        // Return error response
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
} else {
    // Return error for non-POST requests
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
}

// Close database connection
$conn = null;
?> 