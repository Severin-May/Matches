<?php
include('storage.php');
include('auth.php');
include('userstorage.php');

function redirect($page) {
  header("Location: ${page}");
  exit();
}

// input
session_start();
$user_storage = new UserStorage();
$auth = new Auth($user_storage);

$auth->logout();
redirect('index.php');
