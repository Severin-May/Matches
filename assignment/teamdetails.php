<?php
include('teamnmatch.php');
include('storage.php');
include('auth.php');
include('userstorage.php');
include('commentstorage.php');
include('matchstorage.php');
$matchStorage = new MatchStorage();

// print_r($_POST);

// function redirect($page) {
//     header("Location: ${page}");
//     exit();
// }

///input
session_start();
$user_storage = new UserStorage();
$auth = new Auth($user_storage);

function redirect($page) {
    header("Location: ${page}");
    exit();
}

/* finds team by its name */
function findTeam($teams, $name) {
    foreach($teams as $team) {
        if($team['name'] === $name) return $team;
    }
};
/* $team stores team clicked on link */
$team = findTeam($teams, $_GET['name']);
/* filters all matches where $team played either as home or away */
// $filtered_matches = array_filter($matches, function($match) use ($team) {
//     return ($match['home']['id'] === $team['id']) || ($match['away']['id'] === $team['id']);
// });
$filtered_matches = $matchStorage->findMany(function($match) use ($team) {
    return ($match['home']['id'] === $team['id']) || ($match['away']['id'] === $team['id']);
});

function validate($post, &$data, &$errors) {
    if(!isset($post['user'])) {
        $errors['user'] = "User must be set!";
    }
    elseif(trim($post['user']) === '') {
        $errors['user'] = "User cannot be empty!";
    }
    else {
        $data['user'] = $post['user'];
    }

    if(!isset($post['comment'])) {
        $errors['comment'] = "Comment must be set!";
    }
    elseif(trim($post['comment']) === '') {
        $errors['comment'] = "We don't accept empty comments";
    }
    else {
        $data['comment'] = $post['comment'];
    }
    $today = date("Y-m-d");  
    $data['date'] = $today;
    return count($errors) === 0;
}

// main
$data = [];
$errors = [];
$data['teamid'] = $team['id']; 
$commentStorage = new CommentStorage();
if (count($_POST) > 0) {
    if (validate($_POST, $data, $errors)) {
        $commentStorage->add($data);
        // header('Location: teamdetails.php'); // transforming POST request to GET request
        // exit();
    }
}

$comments = $commentStorage->findMany(function($c) use ($team) {
    return $c['teamid'] === $team['id'];
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team details</title>
    <link rel="stylesheet" href="index.css" media="screen">
</head>
<body>
    <div class="topnav">
        <a href="index.php">Home</a>
        <?php if(!$auth->is_authenticated()):?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php else: ?>
        <a href="logout.php">Logout</a>
        <?php endif;?>
    </div>
    <?php if(!$auth->admin()):?>
        <?php if(isset($filtered_matches) && count($filtered_matches) !== 0 ):?>
            <?php if( isset($auth->authenticated_user()['username']) ): ?>
                <h2>Hello,  <?= $auth->authenticated_user()['username'] ?> </h2>
            <?php endif ?>

            <h2>Team details:</h2>
            <h3><?= $team['name'] ?></h3>
            <table>
                <tr>
                    <th>Match #</th>
                    <th>Home</th>
                    <th>Away</th>
                    <th>Date</th>
                    <th>Results</th>
                </tr> <br>
                <?php foreach($filtered_matches as $m) :?>
                    <tr>
                        <td><span><?= $m['id'] ?></span></td> 

                        <?php if($team['id'] === $m['home']['id']) :?>
                            <td><span>✓</span></td> 
                        <?php else :?>  <td><span><?= $teams[$m['home']['id']]['name'] ?></span></td> 
                        <?php endif?>

                        <?php if($team['id'] === $m['away']['id']) :?>
                            <td><span>✓</span></td> 
                        <?php else :?>  <td><span><?= $teams[$m['away']['id']]['name'] ?></span></td> 
                        <?php endif?>

                        <td><span><?= $m['date'] ?></span></td> 
                        
                        <td>
                            <?php if( $m['home']['id'] === $team['id']): ?>
                                <?php if( (int)$m['home']['score'] > (int)$m['away']['score']) : ?>
                                    <span style="color:green"><?= $m['home']['score'] ?></span>
                                <?php elseif( (int)$m['home']['score'] < (int)$m['away']['score']) : ?>
                                    <span style="color:red"><?= $m['home']['score'] ?></span>
                                <?php elseif( (int)$m['home']['score'] == (int)$m['away']['score']) : ?>
                                    <span style="color:yellow"><?= $m['home']['score'] ?></span>
                                <?php endif?>
                            <?php endif?>

                            <?php if( $m['away']['id'] === $team['id']): ?>
                                <?php if( (int)$m['home']['score'] < (int)$m['away']['score']) : ?>
                                    <span style="color:green"><?= $m['away']['score'] ?></span>
                                <?php elseif( (int)$m['home']['score'] > (int)$m['away']['score']) : ?>
                                    <span style="color:red"><?= $m['away']['score'] ?></span>
                                <?php elseif( (int)$m['home']['score'] == (int)$m['away']['score']) : ?>
                                    <span style="color:yellow"><?= $m['away']['score'] ?></span>
                                <?php endif?>
                            <?php endif?> <br>
                        </td>
                    </tr>
                <?php endforeach?> 
            </table>
        <?php else: ?> No Macthes Yet!
        <?php endif ?><br>

        <h2>Comments:</h2>
        <?php if(count($comments) > 0 ) :?>
            <table>
                <tr>
                    <th>Name</th>
                    <th>Comment</th>
                    <th>Date</th>
                </tr>
            <?php foreach($comments as $c):?>
                <tr>
                    <td><?= $c['user'] ?></td>
                    <td><?= $c['comment'] ?></td>
                    <td><?= $c['date']?></td>
                </tr>
            <?php endforeach?>
            </table>
        <?php else :?> No Comments yet!
        <?php endif?>

        <h2>Leave your comment:</h2>
        <?php if(!$auth->is_authenticated()):?>
                <span class="error">You must log in to leave a comment!</span>
        <?php else: ?>
            <form action="" method="post" novalidate>
                <input type="text" name="user" value="<?= $_POST['user'] ?? ' ' ?>"> 
                <?php if (isset($errors['user'])) : ?>
                    <span class="error"><?= $errors['user'] ?></span>
                <?php endif; ?>
                <br><br>
                <textarea type="text" name="comment" placeholder="Add a comment"> </textarea> <br><br>
                <?php if (isset($errors['comment'])) : ?>
                    <span class="error"><?= $errors['comment'] ?></span>
                <?php endif; ?>
                <button>Publish</button>
            </form>
        <?php endif; ?> 

    <?php else: ?>

        <?php if(isset($filtered_matches) && count($filtered_matches) !== 0 ):?>
            <?php if( isset($auth->authenticated_user()['username']) ): ?>
                <h2>Hello,  <?= $auth->authenticated_user()['username'] ?> </h2>
            <?php endif ?>
            <h2>Team details:</h2>
            <h3><?= $team['name'] ?></h3>
            <table>
                <tr>
                    <th>Match #</th>
                    <th>Home</th>
                    <th>Away</th>
                    <th>Date</th>
                    <th>Results</th>
                    <th>Edit</th>
                </tr> <br>
                <?php foreach($filtered_matches as $m) :?>
                    <tr>
                        <td><span><?= $m['id'] ?></span></td> 

                        <?php if($team['id'] === $m['home']['id']) :?>
                            <td><span>✓</span></td> 
                        <?php else :?>  <td><span><?= $teams[$m['home']['id']]['name'] ?></span></td> 
                        <?php endif?>

                        <?php if($team['id'] === $m['away']['id']) :?>
                            <td><span>✓</span></td> 
                        <?php else :?>  <td><span><?= $teams[$m['away']['id']]['name']?></span></td> 
                        <?php endif?>

                        <td><span><?= $m['date'] ?></span></td> 
                        
                        <td>
                            <?php if( $m['home']['id'] === $team['id']): ?>
                                <?php if( (int)$m['home']['score'] > (int)$m['away']['score']) : ?>
                                    <span style="color:green"><?= $m['home']['score'] ?></span>
                                <?php elseif( (int)$m['home']['score'] < (int)$m['away']['score']) : ?>
                                    <span style="color:red"><?= $m['home']['score'] ?></span>
                                <?php elseif( (int)$m['home']['score'] == (int)$m['away']['score']) : ?>
                                    <span style="color:yellow"><?= $m['home']['score'] ?></span>
                                <?php endif?>
                            <?php endif?>

                            <?php if( $m['away']['id'] === $team['id']): ?>
                                <?php if( (int)$m['home']['score'] < (int)$m['away']['score']) : ?>
                                    <span style="color:green"><?= $m['away']['score'] ?></span>
                                <?php elseif( (int)$m['home']['score'] > (int)$m['away']['score']) : ?>
                                    <span style="color:red"><?= $m['away']['score'] ?></span>
                                <?php elseif( (int)$m['home']['score'] == (int)$m['away']['score']) : ?>
                                    <span style="color:yellow"><?= $m['away']['score'] ?></span>
                                <?php endif?>
                            <?php endif?> <br>
                        </td>
                        <td> <a href="edit.php?id=<?= $m['id']?>" >Edit</a></td>
                    </tr>
                <?php endforeach?> 
            </table>
        <?php else: ?> No Macthes Yet!
    <?php endif ?><br>

    <h2>Comments:</h2>
        <?php if(count($comments) > 0 ) :?>
            <table>
                <tr>
                    <th>Name</th>
                    <th>Comment</th>
                    <th>Date</th>
                    <th></th>
                </tr>
            <?php foreach($comments as $c):?>
                <tr>
                    <td><?= $c['user'] ?></td>
                    <td><?= $c['comment'] ?></td>
                    <td><?= $c['date']?></td>
                    <td><a href="delete.php?id=<?= $c['id']?>" >Delete</a></td>
                </tr>
            <?php endforeach?>
            </table>
        <?php else :?> No Comments yet!
        <?php endif?>

    <h2>Leave your comment:</h2>
    <?php if(!$auth->is_authenticated()):?>
            <span class="error">You must log in to leave a comment!</span>
    <?php else: ?>
        <form action="" method="post" novalidate>
            <input type="text" name="user" value="<?= $_POST['user'] ?? ' ' ?>"> 
            <?php if (isset($errors['user'])) : ?>
                <span class="error"><?= $errors['user'] ?></span>
            <?php endif; ?>
            <br><br>
            <textarea type="text" name="comment" placeholder="Add a comment"> </textarea> <br><br>
            <?php if (isset($errors['comment'])) : ?>
                <span class="error"><?= $errors['comment'] ?></span>
            <?php endif; ?>
            <button>Publish</button>
        </form>
    <?php endif ?>
    <?php endif ?>

</body>
</html>