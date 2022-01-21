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
  // username, password not empty
  if(!isset($post['username'])) {
    $errors['username'] = "Username must be set!";
  }
  elseif(trim($post['username']) === '') {
    $errors['username'] = "Username cannot be empty!";
  }
  else {
    $data['username'] = $post['username'];
  }

  if(!isset($post['password'])) {
    $errors['password'] = "Password must be set!";
  }
  elseif(trim($post['password']) === '') {
    $errors['password'] = "Password cannot be empty!";
  }
  else {
    $data['password'] = $post['password'];
  }

  return count($errors) === 0;
}

// main
session_start();
$user_storage = new UserStorage();
$auth = new Auth($user_storage);
$data = [];
$errors = [];
if (count($_POST) > 0) {
  if (validate($_POST, $data, $errors)) {
    $auth_user = $auth->authenticate($data['username'], $data['password']);
    if (!$auth_user) {
      $errors['global'] = "Login error";
    } else {
      $auth->login($auth_user);
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
  <title>Login</title>
  <style>
        input + span {
            font-size: smaller;
            color: red;
        }
    </style>
  <link rel="stylesheet" href="form.css" media="screen">
</head>
<body>
  <h1>Login</h1>
  <?php if (isset($errors['global'])) : ?>
    <p><span class="error"><?= $errors['global'] ?></span></p>
  <?php endif; ?>
  <form action="" method="post" novalidate>
    <div>
      <label class="label" for="username">Username: </label><br>
      <input type="text" name="username" id="username" value="<?= $_POST['username'] ?? "" ?>" required>
      <?php if (isset($errors['username'])) : ?>
        <span class="error"><?= $errors['username'] ?></span>
      <?php endif; ?>
    </div>
    <div>
      <label class="label" for="password">Password: </label><br>
      <input type="password" name="password" id="password" required>
      <?php if (isset($errors['password'])) : ?>
        <span class="error"><?= $errors['password'] ?></span>
      <?php endif; ?>
    </div>
    <div>
      <button type="submit" class="registerbtn">Login</button>
    </div>
  </form>
</body>
</html>