<?php
header('Content-Type: application/json');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/src/Exception.php';
require __DIR__ . '/src/PHPMailer.php';
require __DIR__ . '/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $number   = trim($_POST['number'] ?? '');
    $message  = trim($_POST['message'] ?? '');
    $receiver = trim($_POST['email'] ?? '');

    if ($name === '' || $email === '' || $message === '' || $receiver === '') {
        echo json_encode([
            "status"  => "error",
            "message" => "Please fill all required fields."
        ]);
        exit;
    }

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'khushitejani9624@gmail.com';
        $mail->Password   = 'jxbq myju ynsm rwlv';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('khushitejani9624@gmail.com', 'Website Contact Form');
        $mail->addReplyTo($email, $name);
        $mail->addAddress($receiver);

        $mail->isHTML(true);
        $mail->Subject = "📩 New Contact Form Submission";
        $mail->Body = "
            <h2>New Contact Form Submission</h2>
            <p><strong>Name:</strong> {$name}</p>
            <p><strong>Email:</strong> {$email}</p>
            <p><strong>Phone:</strong> {$number}</p>
            <p><strong>Message:</strong><br>" . nl2br(htmlspecialchars($message)) . "</p>
            <hr>
            <small>Sent on " . date('d M Y, H:i:s') . "</small>
        ";
        $mail->AltBody = "Name: {$name}\nEmail: {$email}\nPhone: {$number}\nMessage:\n{$message}";

        $mail->send();
        echo json_encode([
            "status"  => "success",
            "message" => "Your message has been sent successfully!"
        ]);
    } catch (Exception $e) {
        echo json_encode([
            "status"  => "error",
            "message" => "Message could not be sent. Error: {$mail->ErrorInfo}"
        ]);
    }
} else {
    echo json_encode([
        "status"  => "error",
        "message" => "Invalid request method."
    ]);
}
