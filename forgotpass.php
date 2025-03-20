<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Access-Control-Allow-Origin");

require 'vendor/autoload.php';

include("db.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


$mail = new PHPMailer(true);

$email = $_POST['email'];
$otp = rand(100000, 999999);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'lawassistai07@gmail.com';  // Use a valid email
    $mail->Password = 'xovu cuik lobg tpcn';  // Use an App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    $emailsSent = [];


    $mail->setFrom('lawassistai07@gmail.com', 'Lawassist');
    $mail->addAddress($email, 'Surprise');

    $stmt = $conn->prepare("INSERT INTO otp_table (email, otp, created_at) VALUES (?, ?, NOW())
        ON DUPLICATE KEY UPDATE otp = VALUES(otp), created_at = NOW()");

    $stmt->bind_param("ss", $email, $otp);
    $stmt->execute();
    // Email Body
    $mail->isHTML(true);
    $mail->Body = "
        <html>
        <head>
            <style>
                .email-container {
                    font-family: Arial, sans-serif;
                    background-color: #f4f4f4;
                    padding: 20px;
                    border-radius: 10px;
                    text-align: center;
                    max-width: 500px;
                    margin: auto;
                }
                .otp-code {
                    font-size: 22px;
                    font-weight: bold;
                    color: #2d89ef;
                    margin: 10px 0;
                }
                .footer {
                    font-size: 12px;
                    color: #777;
                    margin-top: 20px;
                }
            </style>
        </head>
        <body>
            <div class='email-container'>
                <h2>🔐 Password Reset Request</h2>
                <p>Hello,</p>
                <p>You have requested to reset your password for <strong>Law Assist AI</strong>. Please use the following One-Time Password (OTP) to verify your request:</p>
                <p class='otp-code'>$otp</p>
                <p>This code is valid for <strong>5 minutes</strong>. Do not share it with anyone.</p>
                <p>If you did not request this, please ignore this email.</p>
                <p class='footer'>© 2025 Law Assist AI | Need help? <a href='mailto:support@lawassistai.com'>Contact Support</a></p>
            </div>
        </body>
        </html>
    ";
    $mail->AltBody = "You have a special memory waiting to be unlocked.";

    if ($mail->send()) {
        $emailsSent[] = $otp;
    }
    $mail->clearAddresses();



    echo json_encode(['status' => 200, 'message' => 'Emails sent successfully']);
} catch (Exception $e) {
    echo json_encode(['status' => 500, 'message' => "Mailer Error: {$mail->ErrorInfo}"]);
}


?>