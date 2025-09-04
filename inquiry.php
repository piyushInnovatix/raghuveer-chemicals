<?php
header('Content-Type: application/json');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/src/Exception.php';
require __DIR__ . '/src/PHPMailer.php';
require __DIR__ . '/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $company_name   = $_POST['company_name'] ?? '';
    $password       = $_POST['password'] ?? '';
    $designation    = $_POST['designation'] ?? '';
    $first_name     = $_POST['first_name'] ?? '';
    $last_name      = $_POST['last_name'] ?? '';
    $email          = $_POST['email'] ?? '';
    $contact_number = $_POST['contact_number'] ?? '';
    $city           = $_POST['city'] ?? '';
    $state          = $_POST['state'] ?? '';
    $country        = $_POST['country'] ?? '';
    $message        = $_POST['message'] ?? '';
    $products       = isset($_POST['products']) ? implode(", ", $_POST['products']) : 'None';

    if (empty($first_name) || empty($email)) {
        echo json_encode(["status" => "error", "message" => "Please fill all required fields."]);
        exit;
    }

    // Create PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // SMTP setup
        $mail->isSMTP();
        $mail->Host       = 'smtp.hostinger.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'sales@raghuveer-chemicals.com';
        $mail->Password   = 'Raghuveer@Sales09'; // Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Send to admin
        $mail->setFrom('sales@raghuveer-chemicals.com', 'Website Contact Form');
        $mail->addAddress('sales@raghuveer-chemicals.com'); // send to yourself
        $mail->addReplyTo($email, $name);

        $mail->isHTML(true);
        $mail->Subject = "Inquiry in Raghuveer Chemicals from {$company_name}";
        $mail->Body = "
            <h2>New Inquiry Details</h2>
            <p><strong>Company Name:</strong> {$company_name}</p>
            <p><strong>Password:</strong> {$password}</p>
            <p><strong>Designation:</strong> {$designation}</p>
            <p><strong>First Name:</strong> {$first_name}</p>
            <p><strong>Last Name:</strong> {$last_name}</p>
            <p><strong>Email:</strong> {$email}</p>
            <p><strong>Contact Number:</strong> {$contact_number}</p>
            <p><strong>City:</strong> {$city}</p>
            <p><strong>State:</strong> {$state}</p>
            <p><strong>Country:</strong> {$country}</p>
            <p><strong>Products Interested:</strong> {$products}</p>
            <p><strong>Message:</strong><br>{$message}</p>
        ";
        $mail->AltBody = "Name: {$name}\nEmail: {$email}\nMessage:\n{$message}";

        $mail->send();

        // Send confirmation to user
        // $mail->clearAddresses();
        // $mail->addAddress($email);
        // $mail->Subject = "✅ Thank you for contacting us";
        // $mail->Body = "
        //     <h2>Hello {$first_name},</h2>
        //     <p>Thank you for reaching out. We have received your inquiry and will get back to you shortly.</p>
        // ";

        $mail->send();

        echo json_encode([
            "status"  => "success",
            "message" => "Your inquiry has been sent successfully."
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            "status"  => "error",
            "message" => "Message could not be sent. Error: {$mail->ErrorInfo}"
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request."
    ]);
}
