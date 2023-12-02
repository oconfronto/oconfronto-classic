<?php

function fetchCronSettings($db): array {
    $result = $db->execute("SELECT * FROM `cron`");
    if (!$result) {
        throw new Exception("Error executing query: " . $db->ErrorMsg());
    }

    $cron = [];
    while ($row = $result->fetchrow()) {
        $cron[$row['name']] = $row['value'];
    }

    return $cron;
}

function updatePlayerStats($db, $diff): void {
    $timedif = ($diff / 60);
    $addhp = (35 * $timedif);
    $addenergy = (10 * $timedif);
    $addmana = (35 * $timedif);

    $db->execute("UPDATE `players` SET 
        hp = IF((hp + ?) > maxhp, maxhp, (hp + ?)), 
        mana = IF((mana + ?) > maxmana, maxmana, (mana + ?)) 
        WHERE hp > 0 AND lutando = 0", [$addhp, $addhp, $addmana, $addmana]);

    $db->execute("UPDATE `players` SET 
        energy = IF((energy + ?) > maxenergy, maxenergy, (energy + ?))", 
        [$addenergy, $addenergy]);
}

function updateCronLast($db, $name, $value): void {
    $db->execute("UPDATE `cron` SET `value` = ? WHERE `name` = ?", [$value, $name]);
}

function cleanupOldData($db, $timeFrameInSeconds): void {
    $olderThanTime = ceil(time() - $timeFrameInSeconds);

    $tablesToCleanup = [
        'mail' => 'time',
        'user_log' => 'time',
        'log_battle' => 'time',
        'logbat' => 'time',
        'revenge' => 'time',
        // 'work' table uses 'start' instead of 'time'
        'work' => 'start',
        'log_gold' => 'time',
        'log_item' => 'time',
        'account_log' => 'time',
        'log_forum' => 'time'
    ];

    foreach ($tablesToCleanup as $table => $columnName) {
        $db->execute("DELETE FROM `$table` WHERE `$columnName` < ?", [$olderThanTime]);
    }
}

/**
 * @return void
 */
function performWorkUpdates($db) {
    $updategeralwork = $db->execute("SELECT * FROM `work` WHERE `status` = 't' AND (`start` + (`worktime` * 3600)) < ?", [time()]);
    if (!$updategeralwork) {
        throw new Exception("Error executing work query: " . $db->ErrorMsg());
    }

    while ($newwork = $updategeralwork->fetchrow()) {
        $updateWorkStatus = $db->execute("UPDATE `work` SET `status` = 'f' WHERE `id` = ?", [$newwork['id']]);
        if (!$updateWorkStatus) {
            throw new Exception("Error updating work status: " . $db->ErrorMsg());
        }

        $updatePlayers = $db->execute("UPDATE `players` SET 
            `gold` = `gold` + ?, 
            `energy` = `energy` - ? 
            WHERE `id` = ?", [($newwork['gold'] * $newwork['worktime']), $newwork['worktime'], $newwork['player_id']]);
        if (!$updatePlayers) {
            throw new Exception("Error updating players: " . $db->ErrorMsg());
        }

        $worklog = "Seu trabalho como " . $newwork['worktype'] . " terminou! Você recebeu <b>" . ($newwork['gold'] * $newwork['worktime']) . " moedas de ouro</b>.";
        addlog($newwork['player_id'], $worklog, $db);
    }
}

try {
    $cron = fetchCronSettings($db);
    $now = time();

    // Reset last
    $diff = ($now - $cron['reset_last']);
    if ($diff >= 60) {
        updatePlayerStats($db, $diff);
        updateCronLast($db, "reset_last", $now);
    }

    // Interest last
    $diff = ($now - $cron['interest_last']);
    if ($diff >= $cron['interest_time']) {
        // Logic for interest calculation
        updateCronLast($db, "interest_last", $now);
    }

    // One week last
    $diff = isset($cron['oneweek_last']) ? ($now - $cron['oneweek_last']) : 0;
    if (isset($cron['oneweek_time']) && $diff >= $cron['oneweek_time']) {
        $db->execute("UPDATE `players` SET `totalbet` = 0");
        updateCronLast($db, "oneweek_last", $now);
    }

    // Health update, IP cleanup, Log cleanup
    cleanupOldData($db, 604800); // 7 days in seconds

    // Work update
    performWorkUpdates($db);

} catch (Exception $e) {
    die($e->getMessage());
}

?>
