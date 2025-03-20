<?php
include("db.php");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the required fields are provided
    if (isset($_POST['id']) && isset($_POST['name']) && isset($_POST['email']) && isset($_POST['password'])) {
        $userId = $_POST['id'];
        $newName = $_POST['name'];
        $newEmail = $_POST['email'];
        $newPassword = $_POST['password'];

        // Hash the password for secure storage
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

        // Prepare the SQL statement to prevent SQL injection
        $stmt = $conn->prepare("UPDATE login  SET name = ?, email = ?, password = ? WHERE id = ?");

        if ($stmt === false) {
            echo json_encode(["status"=>502,"message" => "Error preparing the query."]);
            exit;
        }

        // Bind the parameters
        $stmt->bind_param("sssi", $newName, $newEmail, $hashedPassword, $userId);

        // Execute the query
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(["status"=>506,"message" => "User details updated successfully."]);
            } else {
                echo json_encode(["status"=>508,"message" => "No changes made or user not found."]);
            }
        } else {
            echo json_encode(["status"=>402,"message" => "Error: " . $stmt->error]);
        }

        // Close the statement
        $stmt->close();
    } else {
        echo json_encode(["status"=>404,"message" => "Invalid input or missing required fields (id, name, email, and password)."]);
    }
} else {
    echo json_encode(["status"=>550,"message" => "Invalid request method. Only POST is allowed."]);
}
?>