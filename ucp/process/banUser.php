<?php
    global $currentUser;
    $user = (int) $_GET['userID'];
    if ($userID == 1) {
        header('Location: /user/1/');
        exit;
    }

    $canBan = $mysql->query("SELECT userID FROM privilages WHERE userID = {$currentUser->userID} AND privilage IN ('banUsers')");
    if ($canBan->rowCount()) {
        $mysql->query("UPDATE users SET banned = NOT banned WHERE userID = {$user} LIMIT 1");
    }
    header("Location: /user/{$user}/");
?>
