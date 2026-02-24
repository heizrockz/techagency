<?php
$content = file_get_contents('controllers/AdminController.php');
$lines = explode("\n", $content);
$line544 = $lines[543] ?? '';
echo bin2hex($line544) . "\n";
echo $line544 . "\n";
