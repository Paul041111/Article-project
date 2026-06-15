<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/session.php';

class AuthController {

    public static function login($data) {
        $conn = db();

        $email = $conn->real_escape_string($data['email']);
        $password = $data['password'];

        $result = $conn->query("SELECT * FROM users WHERE email='$email'");

        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {

                setUserSession([
                    "email" => $user['email'],
                    "fullName" => $user['full_name']
                ]);

                echo json_encode([
                    "success" => true,
                    "user" => [
                        "email" => $user['email'],
                        "fullName" => $user['full_name']
                    ]
                ]);
            } else {
                echo json_encode(["success" => false, "error" => "Wrong password"]);
            }
        } else {
            echo json_encode(["success" => false, "error" => "User not found"]);
        }
    }

    public static function signup($data) {
        $conn = db();

        $name = $conn->real_escape_string($data['name']);
        $email = $conn->real_escape_string($data['email']);
        $password = password_hash($data['password'], PASSWORD_DEFAULT);

        $check = $conn->query("SELECT id FROM users WHERE email='$email'");

        if ($check->num_rows > 0) {
            echo json_encode(["success" => false, "error" => "Email exists"]);
            return;
        }

        $conn->query("INSERT INTO users (email,password,full_name)
                      VALUES ('$email','$password','$name')");

        echo json_encode(["success" => true]);
    }

    public static function session() {
        $user = getUserSession();

        if ($user) {
            echo json_encode(["success" => true, "user" => $user]);
        } else {
            echo json_encode(["success" => false]);
        }
    }

    public static function logout() {
        destroySession();
        echo json_encode(["success" => true]);
    }
}