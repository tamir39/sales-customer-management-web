<?php
session_start();

if ($argc > 1) {
    $filePath = $argv[1];
    sleep(60);
    if (file_exists($filePath)) {
        unlink($filePath);
    }
}
?>
