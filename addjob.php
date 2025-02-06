<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
require_once 'config.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
  if(isset($_POST['title']) && !empty($_POST['title'])){
    $title = $_POST['title'];
  } else {
    die('Title cannot be empty');
  }
  if(isset($_POST['description']) && !empty($_POST['description'])){
    $description = $_POST['description'];
  } else {
    die('Description cannot be empty');
  }
  if(isset($_POST['company']) && !empty($_POST['company'])){
    $company = $_POST['company'];
  } else {
    die('Company cannot be empty');
  }
  if(isset($_POST['location']) && !empty($_POST['location'])){
    $location = $_POST['location'];
  } else {
    die('Location cannot be empty');
  }
  $stmt = $conn->prepare("INSERT INTO jobs (title, description, company_name, location) VALUES (?, ?, ?, ?)");
  $stmt->bind_param('ssss', $title, $description, $company, $location);
  $stmt->execute();
  header("Location: index.php");
}

?>
<!DOCTYPE html>
<html>
<head>
<title>Post a Job</title>
</head>
<body>
<h1>Post a Job</h1>
<form method="POST">
<input type="text" name="title" placeholder="Title" maxlength="20" required><br><br>
<textarea name="description" placeholder="Description" rows="5" required></textarea><br><br>
<input type="text" name="company" placeholder="Company" maxlength="20" required><br><br>
<input type="text" name="location" placeholder="Location" maxlength="25" required><br><br>
<button type="submit">Post</button>
</form>
</body>
</html>
