<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Access-Control-Allow-Origin");

require 'db.php'; // Include database connection

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['email']) || !isset($data['otp']) || !isset($data['password'])) {
    echo json_encode(['status' => 400, 'message' => 'Invalid request']);
    exit();
}

$email = $data['email'];
$user_otp = $data['otp'];
$password = $data['password'];

// Check OTP from the database
$stmt = $conn->prepare("SELECT otp, created_at FROM otp_table WHERE email = ? ORDER BY created_at DESC LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $stored_otp = $row['otp'];
    $created_at = strtotime($row['created_at']);
    $current_time = time();
    
    if ($user_otp == $stored_otp) {
        if (($current_time - $created_at) <= 10) { // Check if OTP is within 5 minutes
            // OTP is valid, delete it from database after successful verification
            // $delete_stmt = $conn->prepare("DELETE FROM otp_table WHERE email = ?");
            // $delete_stmt->bind_param("s", $email);
            // $delete_stmt->execute();

            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            $update_stmt = $conn->prepare("update login set password = ? where email = ?");
            $update_stmt->bind_param("ss", $hashedPassword, $email);
            $update_stmt->execute();

            echo json_encode(['status' => 200, 'message' => 'OTP Verified Successfully']);
        } else {
            echo json_encode(['status' => 401, 'message' => 'OTP Expired']);
        }
    } else {
        echo json_encode(['status' => 401, 'message' => 'Invalid OTP']);
    }
} else {
    echo json_encode(['status' => 404, 'message' => 'No OTP found']);
}

$stmt->close();
$conn->close();
?>
