<?php
include('storage.php');
include('commentstorage.php');

function redirect($page) {
    header("Location: ${page}");
    exit();
}

$commentStorage = new CommentStorage();

$id = $_GET['id'];

$commentStorage->delete($id);

redirect('index.php');

?>