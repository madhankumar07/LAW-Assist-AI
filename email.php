<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Access-Control-Allow-Origin");

require 'vendor/autoload.php';


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


    $mail->setFrom('lawassistai07@gmail.com', 'Dev Up');
    $mail->addAddress($email, 'Surprise');

    // Email Body
    $mail->isHTML(true);
    $mail->Subject = 'Password Reset OTP';
    $mail->Body = "
                <p><strong>our OTP for password reset is: $otp. It is valid for 5 minutes.</strong> </p>
            
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