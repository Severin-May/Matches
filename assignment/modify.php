<?php
include('teamdetails.php');
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
    <?php if(isset($filtered_matches) && count($filtered_matches) !== 0 ):?>
        <span>
            <a href="index.php">Home</a>
        </span>
        <span>
            <a href="logout.php">Logout</a>
        </span>
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
                    <?php else :?>  <td><span><?= $m['home']['id'] ?></span></td> 
                    <?php endif?>

                    <?php if($team['id'] === $m['away']['id']) :?>
                        <td><span>✓</span></td> 
                    <?php else :?>  <td><span><?= $m['away']['id'] ?></span></td> 
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
                    <th></th>
                </tr>
            <?php foreach($comments as $c):?>
                <tr>
                    <td><?= $c['user'] ?></td>
                    <td><?= $c['comment'] ?></td>
                    <td>Delete</td>
                </tr>
            <?php endforeach?>
            </table>
        <?php else :?> No Comments yet!
    <?php endif?>

</body>
</html>