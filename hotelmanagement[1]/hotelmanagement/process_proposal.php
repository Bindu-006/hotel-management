<?php
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
$required_fields = ['event_type', 'event_date', 'guest_count', 'location', 'first_name', 'last_name', 'email', 'phone'];
foreach ($required_fields as $field) {
    if (empty($data[$field])) {
        die("Missing required field: $field");
    }
}

// Validate email
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    die("Invalid email format");
}

try {
    // Check if the date and location are already booked
    $check_sql = "SELECT id FROM proposal_requests WHERE event_date = ? AND location = ? AND status != 'cancelled'";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param('ss', $data['event_date'], $data['location']);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Date and location are already booked
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Date Unavailable - The Rajputana Palace</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                body {
                    background-color: #f8f5f0;
                    font-family: "Poppins", sans-serif;
                }
                .error-container {
                    max-width: 800px;
                    margin: 100px auto;
                    padding: 40px;
                    background-color: white;
                    border-radius: 10px;
                    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
                    text-align: center;
                }
                .error-icon {
                    color: #dc3545;
                    font-size: 48px;
                    margin-bottom: 20px;
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
            <div class="error-container">
                <div class="error-icon">⚠</div>
                <h1>Date Unavailable</h1>
                <p>We apologize, but the selected date (<?php echo htmlspecialchars($data['event_date']); ?>) at <?php echo htmlspecialchars($data['location']); ?> is already booked.</p>
                <p>Please select a different date or location for your event.</p>
                <a href="proposal_form.html" class="back-btn">Return to Proposal Form</a>
            </div>
        </body>
        </html>
        <?php
        exit();
    }

    // Insert the proposal request into the database
    $sql = "INSERT INTO proposal_requests (
        event_type, event_date, guest_count, location,
        first_name, last_name, email, phone,
        special_requirements, budget_range,
        status, created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";
    
    $stmt = $conn->prepare($sql);
    $special_requirements = $data['special_requirements'] ?? '';
    $budget_range = $data['budget_range'] ?? '';
    
    $stmt->bind_param('ssisssssss', 
        $data['event_type'],
        $data['event_date'],
        $data['guest_count'],
        $data['location'],
        $data['first_name'],
        $data['last_name'],
        $data['email'],
        $data['phone'],
        $special_requirements,
        $budget_range
    );

    if ($stmt->execute()) {
        $proposal_id = $conn->insert_id;
        
        // Send confirmation email
        $to = $data['email'];
        $subject = "Proposal Request Received - The Rajputana Palace";
        $message = "Dear {$data['first_name']} {$data['last_name']},\n\n";
        $message .= "Thank you for your proposal request with The Rajputana Palace.\n\n";
        $message .= "Request Details:\n";
        $message .= "Proposal ID: $proposal_id\n";
        $message .= "Event Type: {$data['event_type']}\n";
        $message .= "Event Date: {$data['event_date']}\n";
        $message .= "Number of Guests: {$data['guest_count']}\n";
        $message .= "Location: {$data['location']}\n\n";
        $message .= "Our team will review your request and get back to you within 48 hours.\n\n";
        $message .= "Best regards,\nThe Rajputana Palace Team";

        $headers = "From: proposals@rajputanapalace.com";

            $mail->send();
            $email_sent = true;
        } catch (Exception $e) {
            $email_sent = false;
            error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
        
        // Show success page
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Proposal Request Received - The Rajputana Palace</title>
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
                .proposal-details {
                    text-align: left;
                    margin: 30px 0;
                    padding: 20px;
                    background-color: #f8f5f0;
                    border-radius: 5px;
                }
                .proposal-details p {
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
                <h1>Proposal Request Received!</h1>
                <p>Thank you for your interest in The Rajputana Palace.</p>
                
                <div class="proposal-details">
                    <h3>Request Details</h3>
                    <p><strong>Proposal ID:</strong> <?php echo $proposal_id; ?></p>
                    <p><strong>Event Type:</strong> <?php echo htmlspecialchars($data['event_type']); ?></p>
                    <p><strong>Event Date:</strong> <?php echo htmlspecialchars($data['event_date']); ?></p>
                    <p><strong>Number of Guests:</strong> <?php echo htmlspecialchars($data['guest_count']); ?></p>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($data['location']); ?></p>
                </div>

                <?php if (!$email_sent): ?>
                <div class="alert alert-warning">
                    Note: We were unable to send the confirmation email. Please save your Proposal ID for reference.
                </div>
                <?php endif; ?>

                <a href="index.html" class="back-btn">Return to Home</a>
            </div>
        </body>
        </html>
        <?php
    } else {
        die("Failed to submit proposal request: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    die("An error occurred: " . $e->getMessage());
}
?> 