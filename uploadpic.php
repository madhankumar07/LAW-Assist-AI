<?php
include 'db.php'; // Include database connection

// Set upload directory
$upload_dir = "uploads/";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST["user_id"]) || !isset($_FILES["profile_pic"])) {
        echo json_encode(["status" => "error", "message" => "Missing user ID or file"]);
        exit();
    }

    $user_id = intval($_POST["user_id"]);
    $file_name = basename($_FILES["profile_pic"]["name"]);
    $target_file = $upload_dir . uniqid() . "_" . $file_name; // Unique filename

    // Allowed file types
    $allowed_types = ["jpg", "jpeg", "png", "gif"];
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    if (!in_array($file_type, $allowed_types)) {
        echo json_encode(["status" => "error", "message" => "Invalid file type. Allowed: JPG, JPEG, PNG, GIF"]);
        exit();
    }

    // Move file to upload directory
    if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
        $image_url = $target_file; // Change to your actual domain

        // Update the profile picture URL in the database
        $stmt = $conn->prepare("UPDATE login SET profile_pic = ? WHERE id = ?");
        $stmt->bind_param("si", $image_url, $user_id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Profile picture uploaded successfully", "image_url" => $image_url]);
        } else {
            echo json_encode(["status" => "error", "message" => "Database update failed"]);
        }

        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "File upload failed"]);
    }
}

$conn->close();
?>
