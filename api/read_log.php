<?php
$logFile = sys_get_temp_dir() . '/mico_payload.log';
if (file_exists($logFile)) {
    header('Content-Type: text/plain');
    echo file_get_contents($logFile);
} else {
    echo "No payload log found at $logFile";
}
