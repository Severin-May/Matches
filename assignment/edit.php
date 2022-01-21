<?php
include('storage.php');
include('teamnmatch.php');
include('matchstorage.php');
$matchStorage = new MatchStorage();


function redirect($page) {
    header("Location: ${page}");
    exit();
}

// function findMatch($matches, $id) {
//     foreach($matches as $m) {
//         if($m['id'] === $id) return $m;
//     }
// };

//print_r($_GET);
//$match = findMatch($matches, $_GET['id']);
$match = $matchStorage->findById($_GET['id']);
//print_r($match);
function validate($post, &$data, &$errors) {

    if(!isset($post['result1'])) {
      $errors['result1'] = "Result must be set!";
    }
    elseif(trim($post['result1']) === '') {
      $errors['result1'] = "Result cannot be empty!";
    }
    else {
      $data['result1'] = $post['result1'];
    }

    if(!isset($post['result2'])) {
        $errors['result3'] = "Result must be set!";
      }
      elseif(trim($post['result2']) === '') {
        $errors['result2'] = "Result cannot be empty!";
      }
      else {
        $data['result2'] = $post['result2'];
    }
    
    // $test_arr  = explode('/', $post['date']);
    // if(!isset($post['date'])) {
    //   $errors['date'] = "Date must be set!";
    // }
    // elseif(checkdate((int)$test_arr[0], (int)$test_arr[1], (int)$test_arr[2]) === false) {
    //     $errors['date'] = "Date is invalid!";
    // }
    // else {
    //   $data['date'] = date_format($post['date'],"y-m-d");
    // }

    $data['date'] = date("y-m-d", strtotime($post['date']));
    return count($errors) === 0;
}

$data = [];
$errors = [];
if (count($_POST) > 0) {
  if (validate($_POST, $data, $errors)) {
     $match['home']['score'] = $data['result1'];
     $match['away']['score'] = $data['result2'];
     $match['date'] = $data['date'];
     $matchStorage->update($_GET['id'], $match);
     redirect('index.php');
  }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit match</title>
    <link rel="stylesheet" href="index.css" media="screen">
    <link rel="stylesheet" href="form.css" media="screen">
</head>
<body>
<h3>Here you can change the result or the date of the match!</h3>
<h4><?= $teams[$match['home']['id']]['name']?> vs <?= $teams[$match['away']['id']]['name'] ?></h4>
<hr>
<form action="" method="post" novalidate>
<h3>Edit here if you want to change results!</h3>
    <div>
      <label class="label" for="result"><?= $teams[$match['home']['id']]['name']?> : </label><br>
      <input type="text" name="result1" value="<?= $_POST['result1'] ?? "" ?>" required>
      <?php if (isset($errors['result1'])) : ?>
        <span class="error"><?= $errors['result1'] ?></span>
      <?php endif; ?>
      <br>
      <label class="label" for="result"><?= $teams[$match['away']['id']]['name']?> : </label><br>
      <input type="text" name="result2" value="<?= $_POST['result2'] ?? "" ?>" required>
      <?php if (isset($errors['result2'])) : ?>
        <span class="error"><?= $errors['result2'] ?></span>
      <?php endif; ?>
    </div>
    <div>
    <button type="submit" class="registerbtn">Save</button>
    </div>
  </form>
<br><hr>

  <form action="" method="post" novalidate>
  <h3>Edit here if you want to change the date!</h3>
    <div>
      <input type="date" name="date" required>
      <?php if (isset($errors['date'])) : ?>
        <span class="error"><?= $errors['date'] ?></span>
      <?php endif; ?>
    </div>
    <br>
    <div>
    <button type="submit" class="registerbtn">Set</button>
    </div>
  </form>

  <h1>Home score: <?= $matchStorage->findById($_GET['id'])['home']['score'] ?></h1>
  <h1>Away score: <?= $matchStorage->findById($_GET['id'])['away']['score'] ?></h1>
</body>
</html>