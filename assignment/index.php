<?php

include('teamnmatch.php');

$past_matches = array_filter($matches, function($match) {
    return date("Y-m-d", strtotime($match['date'])) < date("Y-m-d");;
});
//TODO: needs to be rewritten
usort($past_matches, function ($a, $b) {
    if($a['date'] > $b['date']) return -1;
    elseif($a['date'] < $b['date']) return 1;
    else return 0;
});

$last_matches = [];
for($i=0;$i<5;$i++) {
    $last_matches [] = $past_matches[$i];
}
//print_r($last_matches);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matches at ELTE</title>
    <link rel="stylesheet" href="index.css" media="screen">
</head>
<body>
    <div class="topnav">
        <a class="active" href="#home">Home</a>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
    </div>
<h2>About the page:</h2>
<p>You can find all the matches played at ELTE Stadium here! We want our fans to be able to follow the results of their favorite teams.</p>
<h2>All teams: </h2>
<ul>
    <?php foreach($teams as $team) :?>
        <li>
            <a href="teamdetails.php?name=<?= $team['name']?>" >
                <?= $team['name']?>
            </a>
        </li>
    <?php endforeach ?>
</ul>

<h2>Last 5 matches: </h2>
    <table>
        <tr>
            <th>Team #1</th>
            <th>Team #2</th>
        </tr>
        <?php foreach($last_matches as $m) :?>
            <tr>
                <td><?= $teams[$m['home']['id']]['name'] ?></td>  
                <td><?= $teams[$m['away']['id']]['name'] ?></td>  
            </tr>
        <?php endforeach ?>
    </table>
    
</body>
</html>