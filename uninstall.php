<?php

if (!defined('WP_UNINSTALL_PLUGIN')) exit;

// Удаление хука
$timestamp = wp_next_scheduled('bl_cron_backup_hook');
wp_unschedule_event($timestamp, 'bl_cron_backup_hook');

remove_action('bl_cron_backup_hook', 'backup_cron_job');

?>
