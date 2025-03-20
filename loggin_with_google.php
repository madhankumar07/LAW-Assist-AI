<?php
session_start();
require 'db.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if(!isset($_POST['token']) || !isset($_POST['email']) ) {
        http_response_code(400);
        echo json_encode(["status" => 400, "message" => "Missing Body"] );
        exit;
    }

        try {
            
            $email     =  $_POST['email'];
            $password  =   "welcome";

            $stmt = $conn -> prepare("INSERT INTO users (email, name, password) VALUES 
            (?, ?, ?, ?) ON DUPLICATE KEY UPDATE email=?");
            $stmt -> bind_param("sssis", $email, $name, $password, $name);
            $stmt -> execute();
            
            // return json_encode(["status" => 200, "aud" => $data['aud'],
            //  "email" => $data['email'], "email_verified"=>$data['email_verified']]);

            http_response_code(200);
            echo json_encode(["status" => 200, "message" => "Success"]);

            exit;
    } catch (Exception $th) {
        http_response_code(500);
        echo json_encode(["status" => 500, "message" => "Internal Server Error : ". $th -> getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["status" => 400, "message" => "Method Not Allowed"]);
}
?>
