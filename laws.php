<?php

include 'db.php';

// Set the content type to JSON
header("Content-Type: application/json");

// Read the JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Check if the article number is provided
if (!isset($data['article']) || empty($data['article'])) {
    echo json_encode(["message" => "Valid article number is required"]);
    exit();
}

// Get the article number from the request data
$article_number = $data['article'];

// Prepare the SQL query to get the article details
$sql = "SELECT title, description FROM laws WHERE article = ?";
$stmt = $conn->prepare($sql);

// Bind the article number as a string
$stmt->bind_param("s", $article_number);
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// Check if any row is returned
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode([
        "title" => $row['title'],
        "description" => $row['description']
    ]);
} else {
    echo json_encode(["message" => "Article not found"]);
}

// Close the connection
$stmt->close();
$conn->close();
?>