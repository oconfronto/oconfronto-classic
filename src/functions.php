<?php

/*************************************/
/*           ezRPG script            */
/*         Written by Zeggy          */
/*  http://code.google.com/p/ezrpg   */
/*    http://www.ezrpgproject.com/   */
/*************************************/


function check_acc(string $secret_key, &$db)
{
    if (!isset($_SESSION['accid']) || !isset($_SESSION['hash'])) {
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit;
    }
    $check = sha1($_SESSION['accid'] . $_SERVER['REMOTE_ADDR'] . $secret_key);
    if ($check != $_SESSION['hash']) {
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit;
    }
    $query = $db->execute("select * from `accounts` where `id`=?", [$_SESSION['accid']]);
    $accarray = $query->fetchrow();
    if ($query->recordcount() == 0) {
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit;
    }
    foreach($accarray as $key => $value) {
        $acc->$key = $value;
    }
    return $acc;
}


//Function to check if user is logged in, and if so, return user data as an object
function check_user(string $secret_key, &$db)
{
    if (!isset($_SESSION['userid'])) {
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit;
    }
    $query = $db->execute("select * from `players` where `id`=?", [$_SESSION['userid']]);
    $userarray = $query->fetchrow();
    $psecpass = $db->GetOne("select `password` from `accounts` where `id`=?", [$userarray['acc_id']]);
    $psecpass = $psecpass . $_SESSION['userid'] . $userarray['acc_id'] . $_SERVER['REMOTE_ADDR'] . $secret_key;
    $psecpass = sha1($psecpass);
    if ($psecpass != $_SESSION['playerhash']) {
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit;
    }
    if ($query->recordcount() == 0) {
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit;
    }
    foreach($userarray as $key => $value) {
        $user->$key = $value;
    }
    return $user;
}



//Gets the number of unread messages
function unread_messages($id, &$db)
{
    $query = $db->getone("select count(*) as `count` from `mail` where `to`=? and `status`='unread'", [$id]);
    return $query['count'];
}

//Gets new log messages
function unread_log($id, &$db)
{
    $query = $db->getone("select count(*) as `count` from `user_log` where `player_id`=? and `status`='unread'", [$id]);
    return $query['count'];
}

//Insert a log message into the user logs
function addlog($id, $msg, &$db): void
{
    $insert['player_id'] = $id;
    $insert['msg'] = $msg;
    $insert['time'] = time();
    $db->autoexecute('user_log', $insert, 'INSERT');
}

//Insert a log message into the error log
function errorlog($msg, &$db): void
{
    $insert['msg'] = $msg;
    $insert['time'] = time();
    $db->autoexecute('log_errors', $insert, 'INSERT');
}

//Insert a log message into the GM log
function gmlog($msg, &$db): void
{
    $insert['msg'] = $msg;
    $insert['time'] = time();
    $db->autoexecute('log_gm', $insert, 'INSERT');
}

//Insert a log message into the forum log
function forumlog($msg, &$db): void
{
    $insert['msg'] = $msg;
    $insert['time'] = time();
    $db->autoexecute('log_forum', $insert, 'INSERT');
}

// Initialize $setting as an empty object
$setting = new stdClass();

// Get all settings variables
$query = $db->execute("select `name`, `value` from `settings`");
while ($set = $query->fetchrow()) {
    $setting->{$set['name']} = $set['value'];
}

//Get the player's IP address
$ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];


//Gets the number of items owned
function item_count($id, $item, &$db)
{
    $query = $db->getone("select count(*) as `count` from `items` where `item_id`=? and `player_id`=?", [$item, $id]);
    return $query['count'];
}
