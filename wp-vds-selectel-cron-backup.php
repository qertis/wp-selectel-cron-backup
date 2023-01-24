<?php

/**
 * Plugin Name: WP VDS Selectel Cron Backup
 * Description: Плагин для ежедневного бекапа на сервере vds.selectel.ru
 * Author:      Denis Baskovsky
 * Author URI:  https://baskovsky.ru
 * Version:     0.0.1
 * Plugin URI:  https://github.com/qertis/wp-selectel-cron-backup
 * Requires at least: 6.0
 * Requires PHP: 8.1
 * License:     MIT
 * Network:     true
 */

// Токены можно сгенерировать здесь: https://vds.selectel.ru/panel/settings/tokens/
const TOKEN = 'YOUR_TOKEN_HERE';

// Название сервера можно получить здесь: https://vds.selectel.ru/panel/scalets/
const SCALET = 'YOUR_SCALET_HERE';

// Создание резервной копии
function createBackup($name) {
    $url = "https://api.vscale.io/v1/scalets/".SCALET."/backup";

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $headers = [
        "X-Token: ".TOKEN,
        "Content-Type: application/json;charset=UTF-8",
    ];
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $data = '{"name":"'.$name.'"}';
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    $resp = curl_exec($curl);
    curl_close($curl);
    return json_decode($resp);
}
// Просмотр списка резервных копий
function findBackup() {
    $url = 'https://api.vscale.io/v1/backups';

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $headers = [
       "X-Token: ".TOKEN,
       "Content-Type: application/json;charset=UTF-8",
    ];
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $resp = curl_exec($curl);
    curl_close($curl);
    return json_decode($resp);
}
// Удаление резервной копии
function removeBackup($id) {
    $url = 'https://api.vscale.io/v1/backups/'.$id;

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_DELETE, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $headers = [
       "X-Token: ".TOKEN,
       "Content-Type: application/json;charset=UTF-8",
    ];
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $resp = curl_exec($curl);
    curl_close($curl);
    return json_decode($resp);
}
// CRON JOB
function backup_cron_job() {
    $backupList = findBackup();

    // Если копия есть - то удаляем ее
    if (!empty($backupList)) {
        $backupFirstID = $backupList[0]->id;
        removeBackup($backupFirstID);
    }
    // Создаем имя вида Baskovsky-Blog_backup_20230124
    $name = 'Baskovsky-Blog_backup_'.date("Ymd");
    try {
        createBackup($name);
        echo 'backup succesful';
    } catch (error) {
        echo 'backup failed';
    }
}

// Запускаем событие каждый день
if (!wp_next_scheduled('vds_backup_cron_job_hook')) {
    wp_schedule_event(time(), 'daily', 'vds_backup_cron_job_hook');
}

// Название хука
add_action('vds_backup_cron_job_hook', 'backup_cron_job');

?>