<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirects to the index page and ends the session
/**
 * @return never
 */
function redirectToIndex() {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit;
}

// Checks if the user account is valid
function check_acc(string $secret_key, $db): stdClass {
    if (!isset($_SESSION['accid']) || !isset($_SESSION['hash'])) {
        redirectToIndex();
    }

    $check = sha1($_SESSION['accid'] . $_SERVER['REMOTE_ADDR'] . $secret_key);
    if ($check != $_SESSION['hash']) {
        redirectToIndex();
    }

    $query = $db->execute("SELECT * FROM `accounts` WHERE `id` = ?", [$_SESSION['accid']]);
    if ($query->recordCount() == 0) {
        redirectToIndex();
    }

    $accArray = $query->fetchRow();
    $acc = new stdClass();
    foreach ($accArray as $key => $value) {
        $acc->$key = $value;
    }

    return $acc;
}

// Checks if user is logged in and returns user data as an object
function check_user(string $secret_key, $db): stdClass {
    if (!isset($_SESSION['userid'])) {
        redirectToIndex();
    }

    $query = $db->execute("SELECT * FROM `players` WHERE `id` = ?", [$_SESSION['userid']]);
    if ($query->recordCount() == 0) {
        redirectToIndex();
    }

    $userArray = $query->fetchRow();
    $psecpass = $db->GetOne("SELECT `password` FROM `accounts` WHERE `id` = ?", [$userArray['acc_id']]);
    $psecpass = sha1($psecpass . $_SESSION['userid'] . $userArray['acc_id'] . $_SERVER['REMOTE_ADDR'] . $secret_key);

    if ($psecpass != $_SESSION['playerhash']) {
        redirectToIndex();
    }

    $user = new stdClass();
    foreach ($userArray as $key => $value) {
        $user->$key = $value;
    }

    return $user;
}

// Gets the number of unread messages
function unread_messages($id, $db) {
    $query = $db->getOne("SELECT COUNT(*) AS `count` FROM `mail` WHERE `to` = ? AND `status` = 'unread'", [$id]);
    return $query ? $query['count'] : 0;
}

// Gets new log messages
function unread_log($id, $db) {
    $query = $db->getOne("SELECT COUNT(*) AS `count` FROM `user_log` WHERE `player_id` = ? AND `status` = 'unread'", [$id]);
    return $query ? $query['count'] : 0;
}

// Insert a log message into the user logs
function addLog($id, $msg, $db): void {
    $db->autoExecute('user_log', ['player_id' => $id, 'msg' => $msg, 'time' => time()], 'INSERT');
}

// Insert a log message into the error log
function errorLog($msg, $db): void {
    $db->autoExecute('log_errors', ['msg' => $msg, 'time' => time()], 'INSERT');
}

// Insert a log message into the GM log
function gmLog($msg, $db): void {
    $db->autoExecute('log_gm', ['msg' => $msg, 'time' => time()], 'INSERT');
}

// Insert a log message into the forum log
function forumLog($msg, $db): void {
    $db->autoExecute('log_forum', ['msg' => $msg, 'time' => time()], 'INSERT');
}

// Gets the number of items owned
function item_count($id, $item, $db) {
    $query = $db->getOne("SELECT COUNT(*) AS `count` FROM `items` WHERE `item_id` = ? AND `player_id` = ?", [$item, $id]);
    return $query ? $query['count'] : 0;
}

// Initialize settings
$setting = new stdClass();
$query = $db->execute("SELECT `name`, `value` FROM `settings`");
while ($set = $query->fetchRow()) {
    $setting->{$set['name']} = $set['value'];
}

// Get the player's IP address
$ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
