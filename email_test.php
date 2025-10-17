<?php
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';   // Gmail SMTP
    $mail->SMTPAuth   = true;
    $mail->Username   = 'pallavirawal679@gmail.com'; // ✅ your Gmail
    $mail->Password   = 'dylt argg qyig ddlz';    // ✅ Gmail App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Recipients
    $mail->setFrom('your_email@gmail.com', 'Warranty Locker');
    $mail->addAddress('receiver_email@gmail.com'); // ✅ test email

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Test Email from Warranty Locker';
    $mail->Body    = '<h3>Hello!</h3><p>This is a test email from your Warranty Locker project.</p>';

    $mail->send();
    echo "✅ Test email sent successfully!";
} catch (Exception $e) {
    echo "❌ Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
