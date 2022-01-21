<?php
include('storage.php');
include('auth.php');
include('userstorage.php');

// functions
function redirect($page) {
  header("Location: ${page}");
  exit();
}
function validate($post, &$data, &$errors) {
  if(!isset($post['username'])) {
    $errors['username'] = "Username must be set!";
  }
  elseif(trim($post['username']) === '') {
    $errors['username'] = "Username cannot be empty!";
  }
  else {
    $data['username'] = $post['username'];
  }

  if(!isset($post['email'])) {
    $errors['email'] = "Email must be set!";
  }
  elseif(trim($post['email']) === '') {
    $errors['email'] = "Email cannot be empty!";
  }
  elseif (!filter_var($post['email'], FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = "Invalid email format!";
  }
  else {
    $data['email'] = $post['email'];
  }

  if( !isset($post['password'][0]) || !isset($post['password'][1]) ) {
    $errors['password'] = "Password must be set!";
  }
  elseif(trim($post['password'][0]) === '' || trim($post['password'][1]) === '') {
    $errors['password'] = "Password cannot be empty!";
  }
  elseif($post['password'][0] !== $post['password'][1] ) {
    $errors['password'] = "Passwords do not match!";
  }
  else {
    $data['password'] = $post['password'][1];
  }

  return count($errors) === 0;
}

// main
$user_storage = new UserStorage();
$auth = new Auth($user_storage);
$errors = [];
$data = [];
if (count($_POST) > 0) {
  if (validate($_POST, $data, $errors)) {
    if ($auth->user_exists($data['username'])) {
      $errors['global'] = "User already exists";
    } else {
      $auth->register($data);
      redirect('index.php');
    } 
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <style>
        input + span {
            font-size: smaller;
            color: red;
        }
  </style>
  <link rel="stylesheet" href="form.css" media="screen">
</head>
<body>
  <h1>Registration</h1>
  <?php if (isset($errors['global'])) : ?>
    <p><span class="error"><?= $errors['global'] ?></span></p>
  <?php endif; ?>
  <form action="" method="post" novalidate>
    <div>
      <label for="username">Username: </label><br>
      <input type="text" name="username" id="username" value="<?= $_POST['username'] ?? "" ?>" required>
      <?php if (isset($errors['username'])) : ?>
        <span class="error"><?= $errors['username'] ?></span>
      <?php endif; ?>
    </div>
    <div>
      <label for="email">Email: </label><br>
      <input type="email" name="email" id="email" required>
      <?php if (isset($errors['email'])) : ?>
        <span class="error"><?= $errors['email'] ?></span>
      <?php endif; ?>
    </div>
    <div>
      <label for="password">Password: </label><br>
      <input type="password" name="password[]" id="password" required>
      <?php if (isset($errors['password'])) : ?>
        <span class="error"><?= $errors['password'] ?></span>
      <?php endif; ?>
    </div>
    <div>
      <label for="password">Password confirmation: </label><br>
      <input type="password" name="password[]" required>
      <?php if (isset($errors['password'])) : ?>
        <span class="error"><?= $errors['password'] ?></span>
      <?php endif; ?>
    </div>
    <div>
      <button type="submit" class="registerbtn">Register</button>
    </div>
  </form>
</body>
</html>