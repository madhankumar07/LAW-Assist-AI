<?php
include("db.php");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check for required fields
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Prepare SQL statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT id, name, password FROM login WHERE email = ?");
        
        if ($stmt === false) {
            echo json_encode(["message" => "Error preparing the query."]);
            exit;
        }

        // Bind the parameter
        $stmt->bind_param("s", $email);

        // Execute the query
        $stmt->execute();

        // Fetch the result
        $result = $stmt->get_result();

        if ($result && $row = $result->fetch_assoc()) {
            // Verify the password
            if (password_verify($password, $row['password'])) {
                echo json_encode([
                    "status"=>200,"message" => "Login successful.",
                    "data" => [
                        "id" => $row['id'],
                        "name" => $row['name'],
                        "email" => $email
                    ]
                ]);
            } else {
                echo json_encode(["status"=>400, "message" => "Invalid password."]);
            }
        } else {
            echo json_encode(["status"=>402,"message" => "No account found with this email."]);
        }

        // Close the statement
        $stmt->close();
    } else {
        echo json_encode(["status"=>502,"message" => "Invalid input or missing required fields."]);
    }
} else {
    echo json_encode(["status"=>450,"message" => "Invalid request method. Only POST is allowed."]);
}
?>