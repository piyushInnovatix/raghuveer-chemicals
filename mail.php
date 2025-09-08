<?php
header('Content-Type: application/json');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/src/Exception.php';
require __DIR__ . '/src/PHPMailer.php';
require __DIR__ . '/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Basic validation
    if (empty($name) || empty($email) || empty($message)) {
        echo json_encode([
            "status" => "error",
            "message" => "All fields are required."
        ]);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            "status" => "error",
            "message" => "Invalid email address."
        ]);
        exit;
    }

    // Create PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // SMTP settings (Hostinger)
        $mail->isSMTP();
        $mail->Host       = 'smtp.hostinger.com'; // or mail.raghuveer-chemicals.com
        $mail->SMTPAuth   = true;
        $mail->Username   = 'info@raghuveer-chemicals.com'; // your Hostinger email
        $mail->Password   = 'Raghuveer@Info09'; // replace with the real password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // use ENCRYPTION_SMTPS if using port 465
        $mail->Port       = 587;

        // Sender & recipient
        $mail->setFrom('info@raghuveer-chemicals.com', 'Website Contact Form');
        $mail->addAddress('info@raghuveer-chemicals.com'); // send to yourself
        $mail->addReplyTo($email, $name);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = "Submission in Raghuveer Chemicals from {$name}";
        $mail->Body    = "
            <h2>New Inquiry</h2>
            <p><strong>Name:</strong> {$name}</p>
            <p><strong>Email:</strong> {$email}</p>
            <p><strong>Message:</strong><br>" . nl2br(htmlspecialchars($message)) . "</p>
            <hr>
            <small>Sent on " . date('d M Y, H:i:s') . "</small>
        ";
        $mail->AltBody = "Name: {$name}\nEmail: {$email}\nMessage:\n{$message}";

        // Send email
        $mail->send();
        echo json_encode([
            "status" => "success",
            "message" => "Your message has been sent successfully!"
        ]);

    } catch (Exception $e) {
        echo json_encode([
            "status" => "error",
            "message" => "Message could not be sent. Error: {$mail->ErrorInfo}"
        ]);
    }

} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request."
    ]);
}
