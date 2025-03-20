<?php
include("db.php");
header("Content-Type: application/json");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check for required fields
    if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['confirm_password'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];

        // Check if password and confirm password match
        if ($password !== $confirmPassword) {
            echo json_encode(["status"=>402,"message" => "Passwords do not match."]);
            exit;
        }

        // Hash the password for storage
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Prepare SQL statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO login(name, email, password, confirm_password) VALUES (?, ?, ?, ?)");
        
        if ($stmt === false) {
            echo json_encode(["status"=>250,"message" => "Error preparing the query."]);
            exit;
        }

        // Bind parameters to the prepared statement
        $stmt->bind_param("ssss", $name, $email, $hashedPassword, $hashedPassword);

        // Execute the query
        if ($stmt->execute()) {
            // Fetch the newly inserted record 
            $last_id = $conn->insert_id;
            $result = $conn->query("SELECT id, name, email FROM login WHERE id = $last_id");

            if ($result && $row = $result->fetch_assoc()) {
                echo json_encode(["status"=>200,"message" => "Record added successfully.", "data" => $row]);
            } else {  
                echo json_encode(["status"=>406,"message" => "Error fetching inserted data."]);
            }
        } else {
            echo json_encode(["status"=>408,"message" => "Error: " . $stmt->error]);
        }

        // Close the prepared statement
        $stmt->close();
    } else {
        echo json_encode(["status"=>502,"message" => "Invalid input or missing required fields."]);
    }
} else {
    // Handle other request methods if necessary
    echo json_encode(["status"=>450,"message" => "Invalid request method. Only POST is allowed."]);
}
?>