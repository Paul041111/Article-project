<?php

  require_once "../config/config.php";
  require_once "controllers/AuthController.php";
  require_once "controllers/ArticleController.php";

  header("Content-Type: application/json");

  $method = $_SERVER["REQUEST_METHOD"];
  $route = $_GET["route"] ?? "";

  $data = json_decode(file_get_contents("php://input"), true);

  if ($method === "GET") {

      if ($route === "session") {
          AuthController::session();
          exit;
      }

      if ($route === "articles") {
          ArticleController::getAll();
          exit;
      }
  }

if ($method === "POST") {

    if ($route === "signup") {
        AuthController::signup($data);
        exit;
    }

    if ($route === "login") {
        AuthController::login($data);
        exit;
    }

    if ($route === "articles") {
        ArticleController::create($data);
        exit;
    }

    if ($route === "logout") {
        AuthController::logout();
        exit;
    }
}

  if ($method === "DELETE") {

      if ($route === "articles") {
          $id = $_GET["id"] ?? "";
          ArticleController::delete($id);
          exit;
      }
  }

  if ($method === "PUT") {

      if ($route === "articles") {
          parse_str($_SERVER["QUERY_STRING"], $q);
          $id = $q["id"] ?? "";

          ArticleController::update($id, $data);
          exit;
      }
  }

  echo json_encode([
      "success" => false,
      "error" => "Invalid route"
  ]);

?>