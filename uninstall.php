<?php

if (!defined('WP_UNINSTALL_PLUGIN')) exit;

// Удаление хука
$timestamp = wp_next_scheduled('vds_backup_cron_job_hook');
wp_unschedule_event($timestamp, 'vds_backup_cron_job_hook');

remove_action('vds_backup_cron_job_hook', 'backup_cron_job');

?>