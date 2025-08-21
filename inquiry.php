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

    $admin_email = "khushitejani9624@gmail.com";

    // Create PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // SMTP setup
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'khushitejani9624@gmail.com';
        $mail->Password   = 'jxbq myju ynsm rwlv'; // Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Send to admin
        $mail->setFrom('khushitejani9624@gmail.com', 'Website Inquiry Form');
        $mail->addReplyTo($email, $first_name . ' ' . $last_name);
        $mail->addAddress($admin_email);

        $mail->isHTML(true);
        $mail->Subject = "📩 New Inquiry from Website";
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

        $mail->send();

        // Send confirmation to user
        $mail->clearAddresses();
        $mail->addAddress($email);
        $mail->Subject = "✅ Thank you for contacting us";
        $mail->Body = "
            <h2>Hello {$first_name},</h2>
            <p>Thank you for reaching out. We have received your inquiry and will get back to you shortly.</p>
        ";

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
