<?php
// filepath: /c:/xampp/htdocs/hotelmanagement/book_experience.php

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hotel_management";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Get the POST data
$data = $_POST;

// Validate required fields
$required_fields = ['firstName', 'lastName', 'email', 'phone', 'experience', 'date'];
foreach ($required_fields as $field) {
    if (empty($data[$field])) {
        die("Missing required field: $field");
    }
}

// Validate email
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    die("Invalid email format");
}

// Check if experience is already booked for the date
$check_sql = "SELECT COUNT(*) as booking_count FROM experience_bookings WHERE experience = ? AND DATE(booking_date) = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param('ss', $data['experience'], $data['date']);
$check_stmt->execute();
$result = $check_stmt->get_result();
$row = $result->fetch_assoc();

if ($row['booking_count'] > 0) {
    die("This experience is already booked for the selected date. Please choose a different date.");
}

try {
    // Insert the booking into the database
    $sql = "INSERT INTO experience_bookings (
        first_name, last_name, email, phone, experience,
        booking_date, booking_time, number_of_guests, special_requirements,
        booking_status, created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";
    
    $stmt = $conn->prepare($sql);
    $guests = $data['guests'] ?? 1;
    $specialRequirements = $data['specialRequirements'] ?? '';
    $time = $data['time'] ?? '00:00:00';
    
    $stmt->bind_param('sssssssss', 
        $data['firstName'],
        $data['lastName'],
        $data['email'],
        $data['phone'],
        $data['experience'],
        $data['date'],
        $time,
        $guests,
        $specialRequirements
    );

    if ($stmt->execute()) {
        $booking_id = $conn->insert_id;
        
        // Send confirmation email
        $to = $data['email'];
        $subject = "Experience Booking Confirmation - The Rajputana Palace";
        $message = "Dear {$data['firstName']} {$data['lastName']},\n\n";
        $message .= "Thank you for booking an experience with The Rajputana Palace.\n\n";
        $message .= "Booking Details:\n";
        $message .= "Booking ID: $booking_id\n";
        $message .= "Experience: {$data['experience']}\n";
        $message .= "Date: {$data['date']}\n";
        $message .= "Time: {$time}\n";
        $message .= "Number of Guests: $guests\n\n";
        $message .= "We will contact you shortly to confirm your reservation.\n\n";
        $message .= "Best regards,\nThe Rajputana Palace Team";

        $headers = "From: bookings@rajputanapalace.com";

        // Show success page
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Booking Confirmation - The Rajputana Palace</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                body {
                    background-color: #f8f5f0;
                    font-family: "Poppins", sans-serif;
                }
                .confirmation-container {
                    max-width: 800px;
                    margin: 100px auto;
                    padding: 40px;
                    background-color: white;
                    border-radius: 10px;
                    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
                    text-align: center;
                }
                .success-icon {
                    color: #28a745;
                    font-size: 48px;
                    margin-bottom: 20px;
                }
                .booking-details {
                    text-align: left;
                    margin: 30px 0;
                    padding: 20px;
                    background-color: #f8f5f0;
                    border-radius: 5px;
                }
                .booking-details p {
                    margin: 10px 0;
                }
                .back-btn {
                    background-color: #8b4513;
                    color: white;
                    padding: 10px 20px;
                    border: none;
                    border-radius: 4px;
                    text-decoration: none;
                    display: inline-block;
                    margin-top: 20px;
                }
                .back-btn:hover {
                    background-color: #cd853f;
                    color: white;
                }
            </style>
        </head>
        <body>
            <div class="confirmation-container">
                <div class="success-icon">✓</div>
                <h1>Booking Confirmed!</h1>
                <p>Thank you for booking an experience with The Rajputana Palace.</p>
                
                <div class="booking-details">
                    <h3>Booking Details</h3>
                    <p><strong>Booking ID:</strong> <?php echo $booking_id; ?></p>
                    <p><strong>Experience:</strong> <?php echo htmlspecialchars($data['experience']); ?></p>
                    <p><strong>Date:</strong> <?php echo htmlspecialchars($data['date']); ?></p>
                    <p><strong>Time:</strong> <?php echo htmlspecialchars($time); ?></p>
                    <p><strong>Number of Guests:</strong> <?php echo htmlspecialchars($guests); ?></p>
                </div>

               
                <div class="alert alert-warning">
                    Note: We have sent the confirmation email. Please save your booking ID for reference.
                </div>
                

                <a href="form.html" class="back-btn">Book Another Experience</a>
            </div>
        </body>
        </html>
        <?php
    } else {
        die("Failed to book experience: " . $stmt->error);
    }

    $stmt->close();
    $check_stmt->close();
    $conn->close();

} catch (Exception $e) {
    die("An error occurred: " . $e->getMessage());
}
?>