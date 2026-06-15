<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/session.php';

class ArticleController {

    public static function getAll() {
        $conn = db();

        $result = $conn->query("SELECT * FROM articles ORDER BY created_at DESC");
        $articles = [];

        while ($row = $result->fetch_assoc()) {
            $articles[] = $row;
        }

        echo json_encode(["success" => true, "articles" => $articles]);
    }

    public static function create($data) {
        $conn = db();
        $user = getUserSession();

        if (!$user) {
            echo json_encode(["success" => false, "error" => "Unauthorized"]);
            return;
        }

        $id = uniqid("art_");
        $title = $conn->real_escape_string($data['title']);
        $category = $conn->real_escape_string($data['category']);
        $excerpt = $conn->real_escape_string($data['excerpt']);
        $content = $conn->real_escape_string($data['content']);
        $image = $conn->real_escape_string($data['image']);

        $conn->query("INSERT INTO articles
        (id,title,category,excerpt,content,image_url,author_email,author_name)
        VALUES
        ('$id','$title','$category','$excerpt','$content','$image',
        '{$user['email']}','{$user['fullName']}')");

        echo json_encode(["success" => true]);
    }

    public static function delete($id) {
        $conn = db();
        $user = getUser();

        if (!$user) {
            echo json_encode(["success" => false, "error" => "Not logged in"]);
            return;
        }

        $email = $user["email"];

        $result = $conn->query("
            DELETE FROM articles 
            WHERE id='$id' 
            AND author_email='$email'
        ");

        if ($conn->affected_rows > 0) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => "Not allowed"]);
        }
    }

    public static function update($id, $data) {
      $conn = db();
      $user = getUser();

      if (!$user) {
          echo json_encode(["success" => false, "error" => "Not logged in"]);
          return;
      }

      $email = $user["email"];

      $title = $conn->real_escape_string($data["title"]);
      $category = $conn->real_escape_string($data["category"]);
      $excerpt = $conn->real_escape_string($data["excerpt"]);
      $content = $conn->real_escape_string($data["content"]);
      $image = $conn->real_escape_string($data["image"]);

      $conn->query("
          UPDATE articles SET
              title='$title',
              category='$category',
              excerpt='$excerpt',
              content='$content',
              image_url='$image'
          WHERE id='$id'
          AND author_email='$email'
      ");

      if ($conn->affected_rows > 0) {
          echo json_encode(["success" => true]);
      } else {
          echo json_encode(["success" => false, "error" => "Not allowed"]);
      }
    }
}